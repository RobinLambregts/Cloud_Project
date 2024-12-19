<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
      let fetchedEvents = [];

      document.addEventListener('DOMContentLoaded', function() {

        fetch('http://localhost:4000/graphql', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            query: `query { events { title date { day month year } } }`
          })
        })        
          .then(response => response.json())
          .then(data => {
            const events = data?.data?.events;
            if (events) {
              console.log('Fetched events:', events);
            } else {
              console.error('Events field is undefined or empty:', data);
            }
          })
  .catch(error => console.error('Network or Parsing Error:', error));


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
