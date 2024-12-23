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
            fetchEvents(window.calendar); // Refresh events
          } else {
            console.error('Error adding event:', data);
          }
        } catch (error) {
          console.error('Error adding event:', error);
          alert('Failed to add the event. Please try again.');
        }
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