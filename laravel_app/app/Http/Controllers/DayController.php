<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WeatherService;

class DayController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

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

        // Get the events from the query parameter
        $events = json_decode($request->query('events'), true);

        // Get weather forecast for the selected date
        $weatherForecast = $this->weatherService->getForecast('diepenbeek', $dayInfo);

        // Pass the date, events, and weather to the view
        return view('day', [
            'dayInfo' => $dayInfo,
            'events' => $events,
            'weatherForecast' => $weatherForecast,
        ]);
    }
}
