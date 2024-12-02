<?php

use Illuminate\Support\Facades\Route;
use HubSpot\Factory;
use GuzzleHttp\Client;
use App\Http\Controllers\HubSpotController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/get-data', [HubSpotController::class, 'getData'])->name('get.data');
Route::post('update-esp', [HubSpotController::class, 'updateEsp'])->name('update.esp');

Route::post('/display-selected-emails', [HubSpotController::class, 'displaySelectedEmails'])->name('display.selected.emails');



Route::post('/fetch-esp', [HubSpotController::class, 'fetchEsp'])->name('fetch-esp');







    Route::get('/', function () {
       return view('hubspot-data');
 });
