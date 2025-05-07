<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class WeatherController extends Controller
{
    public function getWeather()
    {
        $lat = request('lat');
        $lon = request('lon');
        $apiKey = config('services.openweather.key');
    
        if (!$lat || !$lon) {
            return errorResponse(
                message: 'Latitude and longitude are required.',
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }
    
        $response = Http::get("https://api.openweathermap.org/data/2.5/weather", [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $apiKey,
            'units' => 'metric'
        ]);
    
        if ($response->successful()) {
            return successResponse(
                data: [
                    'city' => $response['name'],
                    'temperature' => $response['main']['temp'],
                    'description' => $response['weather'][0]['description'],
                    'Max_temp' => $response['main']['temp_max'],
                    'Min_temp' => $response['main']['temp_min'],
                ],
                message: "Weather data fetched successfully.",
                statusCode: Response::HTTP_OK
            );
        }
        return errorResponse(
            message: 'Weather data not found.',
            statusCode:Response::HTTP_NOT_FOUND
        );
    }
}
