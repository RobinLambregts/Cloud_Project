@extends('layouts.app')

<!-- Add Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<script>
    async function schrijfIn(sport) {
        const name = @json(Auth::user()->name); // Laravel haalt de ingelogde gebruiker op
        console.log(name + " doet mee met " + sport);

        // SOAP-bericht voorbereiden
        const soapMessage = `
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
               <soapenv:Header/>
               <soapenv:Body>
                  <tem:SchrijfIn>
                     <tem:naam>${name}</tem:naam>
                     <tem:sport>${sport}</tem:sport>
                  </tem:SchrijfIn>
               </soapenv:Body>
            </soapenv:Envelope>`;

        try {
            // Fetch-aanroep naar de SOAP-service
            const response = await fetch('http://localhost:5299/Service.asmx', {
                method: 'POST',
                headers: {
                    'Content-Type': 'text/xml; charset=utf-8',
                    'SOAPAction': 'http://tempuri.org/IInschrijfService/SchrijfIn'
                },
                body: soapMessage
            });

            if (response.ok) {
                const result = await response.text();
                console.log('Server response:', result);

                // UI updaten
                const inschrijflink = document.getElementById('inschrijflink' + sport);
                inschrijflink.innerHTML = "INGESCHREVEN!";
                inschrijflink.disabled = true;
                getInschrijvingen(); // Update the list of registrations
            } else {
                console.error('SOAP-aanroep mislukt:', response.statusText);
            }
        } catch (error) {
            console.error('Fout bij SOAP-aanroep:', error);
        }
    }

    function getInschrijvingen() {
        const soapMessage = `
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
               <soapenv:Header/>
               <soapenv:Body>
                  <tem:GetInschrijvingen/>
               </soapenv:Body>
            </soapenv:Envelope>`;

        fetch('http://localhost:5299/Service.asmx', {
            method: 'POST',
            headers: {
                'Content-Type': 'text/xml; charset=utf-8',
                'SOAPAction': 'http://tempuri.org/IInschrijfService/GetInschrijvingen'
            },
            body: soapMessage
        })
        .then(response => response.text())
        .then(data => {
            // Parse the SOAP response
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(data, "text/xml");

            // Extract registrations from the XML
            const registrations = {};
            const keyValuePairs = xmlDoc.getElementsByTagName("d4p1:KeyValueOfstringArrayOfstringty7Ep6D1");
            for (const pair of keyValuePairs) {
                const key = pair.getElementsByTagName("d4p1:Key")[0].textContent.trim();
                const values = pair.getElementsByTagName("d4p1:string");
                const participants = Array.from(values).map(value => value.textContent.trim());
                registrations[key] = participants;
            }

            // Update the DOM for each event
            Object.entries(registrations).forEach(([event, participants]) => {
                const outputUl = document.getElementById(`outputUl${event}`);
                if (outputUl) {
                    let html = participants.map(participant => `<li>${participant}</li>`).join("");
                    outputUl.innerHTML = html; // Update the specific output UL for the event
                }
            });
        })
        .catch(error => console.error("Error fetching or parsing SOAP response:", error));
    }

    // Helper function to parse plain text registrations
    function parsePlainText(rawData) {
        const registrations = {};
        const events = rawData.split(";").map(e => e.trim());
        events.forEach(event => {
            const [eventName, participants] = event.split(":").map(part => part.trim());
            if (eventName && participants) {
                registrations[eventName] = participants.split(",").map(p => p.trim());
            }
        });
        return registrations;
    }

    // Call the function when the page loads
    window.onload = getInschrijvingen;

    getInschrijvingen();
</script>

@section('content')
<div class="container mx-auto p-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-white shadow-md rounded-lg overflow-hidden">
                <div class="card-header bg-blue-500 text-white p-4">{{ __('Dag') }}</div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ __('Welkom ') }} {{ Auth::user()->name }}
                </div>
            </div>

            <h1 class="text-2xl font-bold my-4">Details for {{ $dayInfo }}</h1>

            <!-- Weather forecast section -->
            @if (!empty($weatherForecast))
            <div class="mb-4 p-4 bg-blue-100 border border-blue-200 rounded">
                <h2 class="text-xl font-semibold mb-2">Weather in Diepenbeek</h2>
                <div class="flex gap-4 overflow-x-auto">
                    @foreach ($weatherForecast as $forecast)
                        <div class="flex-1 p-4 bg-white border border-gray-200 text-center rounded">
                            <p>{{ date('H:i', strtotime($forecast['dt_txt'])) }}</p>
                            <p>{{ $forecast['main']['temp'] }}°C</p>
                            <p>{{ $forecast['weather'][0]['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @else
                <p>Geen weergegevens beschikbaar voor {{ $dayInfo }}.</p>
            @endif

            <h2 class="text-xl font-semibold my-4">Events:</h2>
            @if (!empty($events))
                @foreach ($events as $event)
                    <div class="border border-gray-300 p-4 mb-4 rounded">
                        <h3 class="text-lg font-bold">{{ $event['title'] }} - {{ $event['date'] }}</h3>
                        <button onclick="schrijfIn('{{ $event['title'] }}')" id="inschrijflink{{ $event['title'] }}" class="bg-blue-500 text-white rounded p-2 mt-2">IK DOE MEE!</button>
                        @if (Auth::user()->role == 'praesidium')
                            <h5 class="text-md font-semibold mt-4">Inschrijvingen:</h5>
                            <ul id="outputUl{{ $event['title'] }}" class="list-disc pl-5 mb-4"></ul>
                        @endif
                    </div> 
                @endforeach
            @else
                <p>No events found for this day.</p>
            @endif
        </div>
    </div>
</div>
@endsection