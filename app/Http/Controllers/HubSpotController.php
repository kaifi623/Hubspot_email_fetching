<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


class HubSpotController extends Controller
{

public function getData(Request $request)
{

    $hubspotApiKey = config('app.hubspot_api_key');


    $client = new Client();

    try {
        $queryParams = [
            'limit' => 15, // Fetch 100 records at a time
            'properties' => 'firstname,lastname,email,esp',
        ];

        if ($request->after) {
            $queryParams['after'] = $request->after; // Include 'after' parameter if provided
        } elseif ($request->before) {
            $queryParams['before'] = $request->before; // Include 'before' parameter if provided (for "previous" records)
        }
        $response = $client->get('https://api.hubapi.com/crm/v3/objects/contacts', [
            'headers' => [
                'Authorization' => 'Bearer ' . $hubspotApiKey,
                'Content-Type' => 'application/json',
            ],
            'query' => $queryParams,
        ]);

        $contactData = json_decode($response->getBody()->getContents(), true);
        // Step 4: Return data to Blade view
        return view('hubspot-data', ['contacts' => $contactData]);

    } catch (\Exception $e) {
        // Handle errors
        dd([
            'error_message' => $e->getMessage(),
            'response' => $e instanceof \GuzzleHttp\Exception\RequestException
                ? $e->getResponse()->getBody()->getContents()
                : null,
        ]);
    }
}

// fetching esp
public function fetchESP($email=null)
{

     $emailGuardApiKey = config('app.emailguard');

    $url = 'https://api.mails.so/v1/validate?email=' . $email;

    $options = [
        'http' => [
            'header' => "x-mails-api-key: $emailGuardApiKey",
            'method' => 'GET'
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    if ($response === FALSE) {
        die('Error');
    }

    $data = json_decode($response, true);
    if($data['error'] == null)
    {
        return $data['data']['provider'];
    }
    else{
        return false;
    }
}

public function updateEsp(Request $request){
    $hubspotApiKey = config('app.hubspot_api_key');
    $client = new Client();
    $successArray=[];
    $errorsArray = [];
    foreach($request->emails as $index => $email)
    {
        $resultEsp = $this->fetchESP($email);

        if($resultEsp)
        {
            $contact_id = $request->contact_ids[$index];
             // this is for updataion
            $client->patch("https://api.hubapi.com/crm/v3/objects/contacts/$contact_id", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $hubspotApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'properties' => [
                        'esp' => $resultEsp, // Update the ESP custom field
                    ],
                ],
            ]);
            $successArray[]=['email' => $email,'esp' => $resultEsp];
        }
        else{
            $errorsArray[]=['email' => $email,'esp' => $resultEsp];
        }


    }
    if(isset($request->after))
    {
        return redirect()->route('get.data','after='.$request->after)->with('successArray', $successArray)
        ->with('errorsArray', $errorsArray);
    }
    else{
        return redirect()->route('get.data')->with('successArray', $successArray)
        ->with('errorsArray', $errorsArray);
    }
}


}
