@extends('layouts.app')

<!-- Add Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      selectable: true,
      dateClick: function (info) {
        dayClicked(info);
      },
      events: [],
      eventColor: 'red',
    });
  
    calendar.render();
  
    // Fetch events initially and populate the calendar
    fetchEvents(calendar);
  
    // Expose the calendar globally for debugging or dynamic updates
    window.calendar = calendar;
  });
  
  // FETCH EVENTS AND ADD TO CALENDAR
  function fetchEvents(calendar) {
    fetch('http://localhost:4000/graphql', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        query: `query { events { title date { day month year } } }`,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        const events = data?.data?.events;
        if (events) {
          const formattedEvents = events.map((event) => ({
            title: event.title,
            start: `${event.date.year}-${String(event.date.month).padStart(2, '0')}-${String(event.date.day).padStart(2, '0')}`,
          }));
          calendar.removeAllEventSources();
          calendar.addEventSource(formattedEvents);
          console.log('Fetched and added events:', formattedEvents);
        } else {
          console.error('Events field is undefined or empty:', data);
        }
      })
      .catch((error) => console.error('Network or Parsing Error:', error));
  }
  async function getBestSport() {
    try {
      const response = await fetch('http://127.0.0.1:7000');
      if (!response.ok) {
        throw new Error(`Network response was not ok: ${response.statusText}`);
      }
      const data = await response.json();
      return data; // Assuming the Flask server sends a JSON object with the `best_sport` field
    } catch (error) {
      console.error('Error fetching the best sport:', error);
      alert('Staat de Python server aan?');
      return null; // Return null or an appropriate fallback value
    }
  }
  
  // ADD EVENT
  async function addEvent() {
    const date = document.getElementById('eventDate').value; // Input date (YYYY-MM-DD)
    if (!date) {
      alert('Please select a valid date.');
      return;
    }
  
    const title = await getBestSport(); // Await the resolved value
  
    if (!title) {
      alert('Failed to fetch the best sport. Please try again.');
      return;
    }
  
    try {
      const response = await fetch('http://localhost:4000/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify({
          query: `
            mutation {
              addEvent(title: "${title.best_sport}", date: "${date}") {
                title
                date {
                  day
                  month
                  year
                }
              }
            }
          `,
        }),
      });
    
      if (!response.ok) {
        throw new Error(`Network response was not ok: ${response.statusText}`);
      }
    
      const data = await response.json();
      const event = data?.data?.addEvent;
    
      if (event) {
        console.log('Added event:', event);
        fetchEvents(window.calendar);
        const message = `New event added: ${event.title} on ${event.date.year}-${String(event.date.month).padStart(2, '0')}-${String(event.date.day).padStart(2, '0')}`;
        client.publish('events', message);
        console.log('Sent MQTT message:', message);
      } else {
        console.error('Error adding event:', data);
      }
    } catch (error) {
      console.error('Error adding event:', error);
      alert('Failed to add the event. Please try again.');
    }
  }

  function dayClicked(info) {
    const selectedDate = info.dateStr; // Format: YYYY-MM-DD
    
    // Fetch events for the selected date from the GraphQL server
    fetch('http://localhost:4000/graphql', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        query: `
          query GetEvents($date: String) {
            events(date: $date) {
              title
              date {
                day
                month
                year
              }
            }
          }
        `,
        variables: { date: selectedDate },
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.errors) {
          console.error('GraphQL Errors:', data.errors);
          alert('Failed to fetch events. Please try again.');
          return;
        }
      
        const events = data?.data?.events || [];
        const formattedEvents = events.map((event) => ({
          title: event.title,
          date: `${event.date.year}-${String(event.date.month).padStart(2, '0')}-${String(event.date.day).padStart(2, '0')}`,
        }));
        
        console.log('Events for the selected date:', formattedEvents);
      
        // Encode events for URL
        const eventsParam = encodeURIComponent(JSON.stringify(formattedEvents));
        window.location.href = `/day?dayInfo=${selectedDate}&events=${eventsParam}`;
      })
      .catch((error) => {
        console.error('Error fetching events for the selected date:', error);
        alert('Failed to fetch events for the selected date. Please try again.');
      });
  }

</script>

@section('content')
<div class="container mx-auto p-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-white shadow-md rounded-lg overflow-hidden">
                <div class="card-header bg-blue-500 text-white p-4">{{ __('Kalender') }}</div>
                <div class="card-body p-4">
                  @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    {{ __('Welkom ') }} {{ Auth::user()->name }}
                </div>
            </div>
                <div id='calendar' class="mt-4"></div>
                @if (Auth::user()->role === 'praesidium')
                  <div class="mt-4">
                    <input type='date' id='eventDate' class="border rounded p-2" />
                    <button onclick='addEvent()' class="bg-blue-500 text-white rounded p-2 ml-2">Add Event</button>
                  </div>
                @endif
        </div>
    </div>
</div>
@endsection