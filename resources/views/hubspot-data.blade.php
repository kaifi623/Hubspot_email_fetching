<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HubSpot Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">

    <div class="container my-4">
        <h1 class="text-center mb-4">HubSpot Contacts</h1>

        <!-- Success Message -->
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Error Message -->
        @if (isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="mb-4">
            @if(request('after'))
                @if((isset($contacts) && count($contacts['results']) > 0))
                    <a href="{{ route('get.data','before='.$contacts['results'][0]['id']) }}" class="btn btn-primary">Pravious</a>
                @endif
            @endif

            @if (isset($contacts['paging']['next']['after']))
                <a href="{{ route('get.data', 'after=' . $contacts['paging']['next']['after'] ?? null) }}" class="btn btn-secondary">Next</a>
            @endif
        </div>

        <!-- Success and Error Arrays -->
        @if (session('successArray') && count(session('successArray')) > 0)
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">{{ count(session('successArray')) }} Record ESP Updated Successfully!</h4>
            <ul>
                @foreach (session('successArray') as $success)
                <li>{{ $success['email'] }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('errorsArray') && count(session('errorsArray')) > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4 class="alert-heading">These Contacts ESP are not updated</h4>
            <ul>
                @foreach (session('errorsArray') as $error)
                <li>{{ $error['email'] }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif


        <!-- Display Contacts -->
        @if (isset($contacts) && count($contacts['results']) > 0)
        <form method="post" action="{{ route('update.esp') }}">
            @if (request('after'))
            <input type="hidden" name="after" value="{{ request('after') ?? null }}" />
            @endif
            @csrf

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>ESP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contacts['results'] as $index => $contact)
                        <input type="hidden" name="contact_ids[]" value="{{ $contact['id'] }}" />
                        <input type="hidden" name="emails[]" value="{{ $contact['properties']['email'] }}" />
                        <tr>
                            <td>
                                {{ $contact['properties']['firstname'] ?? 'N/A' }} {{ $contact['properties']['lastname'] ?? '' }}
                            </td>
                            <td>
                                {{ $contact['properties']['email'] ?? 'N/A' }}
                            </td>
                            <td>
                                {{ $contact['properties']['esp'] ?? 'Not Updated' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="submit" class="btn btn-success">Update ESP</button>
        </form>
        @else
        <p class="text-center text-muted">No contacts available. Click "Fetch Contacts" to load data.</p>
        @endif


    </div>

</body>

</html>
