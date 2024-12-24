<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Details</title>
</head>
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
<body>
    <h1>Details for {{ $dayInfo }}</h1>

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

    <a href="/kalender">Back to calender</a>
</body>
</html>
