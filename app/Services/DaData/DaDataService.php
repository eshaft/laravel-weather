<?php

namespace App\Services\DaData;

use App\Exceptions\DaDataException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use function GuzzleHttp\json_decode;

class DaDataService
{
    /**
     * @var Client
     */
    protected $guzzle;

    /**
     * @var string
     */
    protected $uri = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address';

    /**
     * @var mixed
     */
    protected $token;

    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;

        $this->token = env('DADATA_KEY');
    }

    public function getCityByIp($ip)
    {
        try {
            $response = $this->guzzle->get($this->uri, [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Token ' . $this->token
                ],
                RequestOptions::QUERY => [
                    'ip' => $ip,
                ]
            ]);

            $content = json_decode($response->getBody()->getContents());

            if (empty($content->location->data->city)) {
                throw new DaDataException('City not found', 404);
            }

            return $content->location->data->city;
        } catch (\Exception $e) {
            throw new DaDataException($e->getMessage(), $e->getCode());
        }
    }
}
