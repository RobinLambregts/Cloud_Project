<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
      const fetchedEvents = [];

      document.addEventListener('DOMContentLoaded', function() {
        const query = `
          query {
            events {
              date {
                day
                month
                year
              }
              title
            }
          }
        `;

        fetch('http://127.0.0.1:4000/graphql', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({ query: query })
        })
        .then(r => r.json())
        .then(data => {
          console.log('data returned:', data);
          
          // Format event data for FullCalendar
          fetchedEvents = data.data.events.map(event => ({
            title: event.title,
            start: `${event.date.year}-${String(event.date.month).padStart(2, '0')}-${String(event.date.day).padStart(2, '0')}`,
          }));

          // Add events to the calendar
          calendar.addEventSource(events);
        })
        .catch(error => console.error('Error fetching data:', error));

        console.log('fetchedEvents:', fetchedEvents);

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          events: fetchedEvents,
          selectable: true,
          dateClick: function(info){
            alert('Date: ' + info.dateStr);
          }
        });
        calendar.render();
      });
    </script>
  </head>
  <body>
    <div id='calendar'></div>
  </body>
</html>
