<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WeatherTest extends TestCase
{
    /**
     * Show weather with success status
     */
    public function testShowWeatherSuccess()
    {
        $response = $this->post('/api/weather', [
            'city' => 'Краснодар',
            'units' => 'metric'
        ]);

        $response->assertStatus(200);
    }

    /**
     * Show weather with error status
     */
    public function testShowWeatherError()
    {
        $response = $this->post('/api/weather', [
            'city' => 'fnfbsdvsds',
            'units' => 'metric'
        ]);

        $response->assertStatus(500);
    }

    /**
     * Show weather by location success
     */
    public function testDetectLocationSuccess()
    {
        $response = $this->post('/api/location', [
            'units' => 'metric'
        ], ['REMOTE_ADDR' => '85.26.184.27']);

        $response->assertStatus(200);
    }

    /**
     * Show weather by location error
     */
    public function testDetectLocationError()
    {
        $response = $this->post('/api/location', [
            'units' => 'metric'
        ], ['REMOTE_ADDR' => '10.0.0.1']);

        $response->assertStatus(500);
    }
}
