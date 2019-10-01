<?php

declare(strict_types=1);

namespace App\Services\OpenWeatherMap;

use App\Exceptions\WeatherException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Redis;
use function GuzzleHttp\json_decode;

/**
 * Class OpenWeatherMapService
 * @package App\Services\OpenWeatherMap
 */
class OpenWeatherMapService
{
    /**
     * @var Client
     */
    protected $guzzle;

    /**
     * @var string
     */
    protected $uri = 'http://api.openweathermap.org/data/2.5/weather';

    /**
     * @var mixed
     */
    protected $appid;

    /**
     * OpenWeatherMapService constructor.
     * @param Client $guzzle
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;

        $this->appid = env('OPEN_WEATHER_KEY');
    }

    /**
     * Get weather by city
     *
     * @param $city
     * @param $units
     * @return mixed
     * @throws WeatherException
     */
    public function getWeatherByCity(string $city, string $units): object
    {
        if (Redis::exists($city.':'.$units)) {
            $content = Redis::get($city.':'.$units);
        } else {
            try {
                $response = $this->guzzle->get($this->uri, [
                    RequestOptions::QUERY => [
                        'q' => $city,
                        'units' => $units,
                        'lang' => 'ru',
                        'appid' => $this->appid
                    ]
                ]);

                $content = $response->getBody()->getContents();

                Redis::set($city.':'.$units, $content, 'EX', 600);
            } catch (\Exception $e) {
                throw new WeatherException($e->getMessage(), $e->getCode());
            }
        }

        return json_decode($content);
    }
}
