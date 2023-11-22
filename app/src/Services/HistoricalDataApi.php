<?php

namespace App\Services;

use App\Exceptions\InvalidSymbolException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Fetches Historical Data from The API.
 * It is set as an Adapter (using GoF Adapter pattern) over the Http client for specific API.
 */
class HistoricalDataApi
{
    const API_URL="https://yh-finance.p.rapidapi.com/stock/v3/get-historical-data";
    const API_HOST="yh-finance.p.rapidapi.com";
    private $api_key;

    private HttpClientInterface $client;
    public function __construct(HttpClientInterface $http, String $api_key)
    {
        $api_key=trim($api_key);
        if(empty($api_key)){
            throw new \InvalidArgumentException("Api key is not a valid String");
        }
        $this->api_key=$api_key;
        $this->client=$http;
    }

    /**
     * Retrieve Historical Data for a company
     * @param string $symbol Company Symbol that is used to fetch the historical data
     * @return array With the result from the api
     * @throws TransportExceptionInterface In case of failed http request
     * @throws InvalidSymbolException In case of empty string at symbol param
     */
    public function fetch(string $symbol):array
    {
        $symbol=strtoupper(trim($symbol));
        if(empty($symbol)){
            throw new InvalidSymbolException();
        }

        $response = $this->client->request(
            'GET',
            self::API_URL,
            [
                'headers'=>[
                    'X-RapidAPI-Host'=>self::API_HOST,
                    // Constructor check for the key validity
                    'X-RapidAPI-Key'=>$this->api_key
                ],
                'query' => [
                    'symbol' => $symbol,
                ],
            ]
        );

        return $response->toArray();
    }
}