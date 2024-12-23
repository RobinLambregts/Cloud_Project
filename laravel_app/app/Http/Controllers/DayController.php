<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DayController extends Controller
{
    /**
     * Show the details for the selected day.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        // Get the selected date
        $dayInfo = $request->query('dayInfo');

        // Validate the date format
        if (!\DateTime::createFromFormat('Y-m-d', $dayInfo)) {
            return redirect()->route('calendar')->withErrors('Invalid date format.');
        }

        // Get the events from the query parameter
        $events = json_decode($request->query('events'), true);

        // Pass the date and events to the view
        return view('day', [
            'dayInfo' => $dayInfo,
            'events' => $events,
        ]);
    }
}
