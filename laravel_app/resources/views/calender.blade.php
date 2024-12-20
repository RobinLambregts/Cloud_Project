<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          selectable: true,
          dateClick: function (info) {
            alert('Date: ' + info.dateStr);
          },
          events: [], // Initialize with an empty array, events will be added later
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
              calendar.removeAllEventSources(); // Clear existing event sources
              calendar.addEventSource(formattedEvents); // Add new events
              console.log('Fetched and added events:', formattedEvents);
            } else {
              console.error('Events field is undefined or empty:', data);
            }
          })
          .catch((error) => console.error('Network or Parsing Error:', error));
      }
      
      // ADD EVENT
      function addEvent() {
        const date = document.getElementById('eventDate').value; // Input date (YYYY-MM-DD)
        const title = 'Best Sport';
      
        fetch('http://localhost:4000/graphql', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            query: `
              mutation {
                addEvent(title: "${title}", date: "${date}") {
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
        })
          .then((response) => response.json())
          .then((data) => {
            const event = data?.data?.addEvent;
            if (event) {
              console.log('Added event:', event);
              fetchEvents(window.calendar); // Re-fetch and refresh events after adding a new one
            } else {
              console.error('Event field is undefined or empty:', data);
            }
          })
          .catch((error) => console.error('Network or Parsing Error:', error));
      }
    </script>
  </head>
  <body>
    <div id='calendar'></div>
    @if (Auth::user()->role === 'praesidium')
      <div>
        <input type='date' id='eventDate' />
        <button onclick='addEvent()'>Add Event</button>
      </div>
    @endif
  </body>
</html>
