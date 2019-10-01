<?php

namespace App\Http\Controllers;

use App\Exceptions\DaDataException;
use App\Exceptions\WeatherException;
use App\Http\Requests\LocationRequest;
use App\Http\Requests\WeatherRequest;
use App\Services\DaData\DaDataService;
use App\Services\OpenWeatherMap\OpenWeatherMapService;

/**
 * Class WeatherController
 * @package App\Http\Controllers
 */
class WeatherController extends Controller
{
    /**
     * @var OpenWeatherMapService
     */
    protected $weatherService;

    /**
     * WeatherController constructor.
     * @param OpenWeatherMapService $weatherService
     */
    public function __construct(OpenWeatherMapService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Show weather data
     *
     * @param WeatherRequest $request
     * @param OpenWeatherMapService $weatherService
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(WeatherRequest $request)
    {
        $data = $request->validated();

        return $this->getWeather($data['city'], $data['units']);
    }

    /**
     * Detect location by ip and show weather data
     *
     * @param LocationRequest $request
     * @param DaDataService $daDataService
     * @return \Illuminate\Http\JsonResponse
     */
    public function location(LocationRequest $request, DaDataService $daDataService)
    {
        $ip = request()->ip();

        $data = $request->validated();

        try {
            $city = $daDataService->getCityByIp($ip);
        } catch (DaDataException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }

        return $this->getWeather($city, $data['units']);
    }

    /**
     * Get weather from service
     *
     * @param $city
     * @param $units
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getWeather($city, $units)
    {
        try {
            $response = $this->weatherService->getWeatherByCity($city, $units);
            return response()->json([
                'success' => true,
                'weather' => $response
            ]);
        } catch (WeatherException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
