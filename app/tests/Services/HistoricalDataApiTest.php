<?php

namespace App\Tests\Services;

use App\Services\HistoricalDataApi;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\MockHttpClient;

class HistoricalDataApiTest extends TestCase
{
    const JSON_RESPONSE_MINI=__DIR__."/../fixtures/historicalDataJsonMini.json";

    private function getMockedHttpClientWithSucessfullResponse(&$responseJson=null)
    {
        $contents = file_get_contents(self::JSON_RESPONSE_MINI);
        $expectedResponse = json_decode($contents,true);

        $responseJson = $expectedResponse;
        return new MockHttpClient([
            new JsonMockResponse($expectedResponse)
        ]);

    }

    public function testFetchApi()
    {
        $httpClientMock = $this->getMockedHttpClientWithSucessfullResponse($expectedResponse);

        $api = new HistoricalDataApi($httpClientMock);
        $api->setApiKey('lalalala');

        $response=$api->fetch('GOOG');
        $this->assertIsArray($response);
        $this->assertEquals($expectedResponse,$response);
    }

    public function testEmptyApiKey()
    {
        $httpClientMock = $this->getMockedHttpClientWithSucessfullResponse();

        $api = new HistoricalDataApi($httpClientMock);
        $api->setApiKey("");

        $this->expectException(\RuntimeException::class);
        $api->fetch("GOOG");
    }

    public function testEmptyNoApiKeySet()
    {
        $httpClientMock = $this->getMockedHttpClientWithSucessfullResponse();

        $api = new HistoricalDataApi($httpClientMock);

        $this->expectException(\RuntimeException::class);
        $api->fetch("GOOG");
    }

    public function testEmptySymbol()
    {
        $httpClientMock = $this->getMockedHttpClientWithSucessfullResponse($expectedResponse);

        $api = new HistoricalDataApi($httpClientMock);
        $api->setApiKey("lalalala");
        $this->expectException(\Exception::class);
        $response=$api->fetch('');
    }
}