<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='utf-8' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>

      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          events: [
                {
                    title: 'Event 1',
                    start: '2024-12-15'
                },
                {
                    title: 'Event 2',
                    start: '2024-12-10',
                    end: '2024-12-20'
                },
                {
                    title: 'Event 3',
                    start: '2023-10-09T12:30:00',
                    allDay: false // will make the time show
                }
            ]
        });
        calendar.render();
      });

    </script>
  </head>
  <body>
    <div id='calendar'></div>
  </body>
</html>