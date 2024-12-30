<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = '164940a622a8b6e482f3086408d2c133';
        $this->baseUrl = 'https://api.openweathermap.org/data/2.5/';
    }

    /**
     * Get weather forecast for a specific city and date.
     *
     * @param string $city
     * @param string $date (format: Y-m-d)
     * @return array|null
     */
    public function getForecast($city, $date)
    {
        // Validate date format
        if (!\DateTime::createFromFormat('Y-m-d', $date)) {
            throw new \InvalidArgumentException('Invalid date format. Use Y-m-d.');
        }

        // Check cache first
        $cacheKey = "weather_{$city}_{$date}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Fetch 5-day forecast (3-hour intervals)
        $response = Http::get("{$this->baseUrl}forecast", [
            'q' => $city,
            'appid' => $this->apiKey,
            'units' => 'metric',
        ]);

        if ($response->successful()) {
            $forecastData = $response->json();

            // Filter forecast for the specified date
            $filteredForecast = array_filter($forecastData['list'], function ($entry) use ($date) {
                // Extract only the date (Y-m-d) part from the 'dt_txt' field
                $forecastDate = substr($entry['dt_txt'], 0, 10);
                return $forecastDate === $date;
            });

            // Cache the result for 1 hour
            Cache::put($cacheKey, $filteredForecast, 3600);

            return $filteredForecast;
        }

        // Handle different types of errors
        if ($response->clientError()) {
            throw new \Exception('Client error: ' . $response->body());
        }

        if ($response->serverError()) {
            throw new \Exception('Server error: ' . $response->body());
        }

        return null;
    }
}