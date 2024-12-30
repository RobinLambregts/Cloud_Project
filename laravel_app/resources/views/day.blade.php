@extends('layouts.app')

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
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dag') }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ __('Welkom ') }} {{ Auth::user()->name }}
                </div>
            </div>

            <h1>Details for {{ $dayInfo }}</h1>

            <!-- Weather forecast section -->
            @if (!empty($weatherForecast))
            <div style="margin-bottom: 20px; padding: 10px; background: #f0f8ff; border: 1px solid #ddd;">
                <h2>Weather in Diepenbeek</h2>
                <div style="display: flex; gap: 20px; width: 100%; overflow-x: auto;">
                    @foreach ($weatherForecast as $forecast)
                        <div style="flex: 1 1 200px; padding: 10px; background: #fff; border: 1px solid #ddd; text-align: center;">
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

            <h2>Events:</h2>
            @if (!empty($events))
                @foreach ($events as $event)
                    <div style="border: 1px solid black; padding: 10px; margin-bottom: 10px">
                        <h3>{{ $event['title'] }} - {{ $event['date'] }}</h3>
                        <button onclick="schrijfIn('{{ $event['title'] }}')" id="inschrijflink{{ $event['title'] }}">IK DOE MEE!</button>
                        @if (Auth::user()->role == 'praesidium')
                            <h5>Inschrijvingen:</h5>
                            <ul id="outputUl{{ $event['title'] }}" style="margin-bottom: 30px"></ul>
                        @endif
                    </div> 
                @endforeach
            @else
                <p>No events found for this day.</p>
            @endif

            <a href="/kalender">Back to calendar</a>
        </div>
    </div>
</div>
@endsection