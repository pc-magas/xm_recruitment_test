<?php

namespace App\Tests\Services;

use App\Services\HistoricalDataApi;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\MockHttpClient;

class HistoricalDataApiTest extends TestCase
{
    const JSON_RESPONSE_MINI=__DIR__."/../fixtures/historicalDataJsonMini.json";

    public function testFetchApi()
    {
        $contents = file_get_contents(self::JSON_RESPONSE_MINI);
        $expectedResponse = json_decode($contents,true);

        $httpClientMock = new MockHttpClient([
            new JsonMockResponse($expectedResponse)
        ]);

        $api = new HistoricalDataApi($httpClientMock,'lalalala');

        $response=$api->fetch('GOOG');
        $this->assertIsArray($response);
        $this->assertEquals($expectedResponse,$response);
    }
}