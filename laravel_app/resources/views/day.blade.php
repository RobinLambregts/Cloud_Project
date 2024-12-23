<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Details</title>
</head>
<body>
    <h1>Details for {{ $dayInfo }}</h1>

    <h2>Events:</h2>
    @if (!empty($events))
        <ul>
            @foreach ($events as $event)
                <li>{{ $event['title'] }} - {{ $event['date'] }}</li>
            @endforeach
        </ul>
    @else
        <p>No events found for this day.</p>
    @endif

    <a href="/kalender">Back to calender</a>
</body>
</html>
