<?php

namespace App\Tests\Services;

use App\Exceptions\InvalidDateRangeException;
use App\Repository\CompanySymbolRepository;
use App\Services\HistoricalDataApi;
use App\Services\SearchStockService;

use http\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;
class SearchStockServiceTest extends TestCase
{
    // Making a whole class for just a constant seem like a waste of time
    const JSON_RESPONSE_MINI=__DIR__."/../fixtures/historicalDataJsonMini.json";

    // This file contains a subset of the data in file above
    const JSON_RETURNED_VALUES_SUBSET=__DIR__."/../fixtures/historicalDataJsonSubset.json";

    /**
     *
     * There was a manual subset of data fetched in JSON_RETURNED_VALUES_SUBSET
     * The
     *
     * @param $date_min
     * @param $date_max
     * @return array
     */
    private function getSearchResultSubset(&$date_min,&$date_max){
        $returnedSubset = json_decode(file_get_contents(self::JSON_RETURNED_VALUES_SUBSET),true);
        usort($returnedSubset,function ($a,$b){
            if ((int)$a['date']==(int)$b['date']) {
                return 0;
            }
            return ((int)$a['date'] < (int)$b['date']) ? -1 : 1;
        });

        $date_min = $returnedSubset[0]['date'];
        $date_max = $returnedSubset[ count($returnedSubset) - 1]['date'];

        return $returnedSubset;
    }

    private function getMaxDateFromFixtures()
    {
        $data = json_decode(file_get_contents(self::JSON_RESPONSE_MINI),true);

        return max(
            array_map(function($item){
                return (int)$item['date'];
                },$data['prices'])
        );
    }

    public function testfetchData()
    {
        $symbolRepositoryMock = $this->getMockBuilder(CompanySymbolRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        // We need to know for non-empty values
        $symbolRepositoryMock->method('findOneBy')->willReturn(['symbol'=>'GOOG']);

        $historicalDataApiMock = $this->createMock(HistoricalDataApi::class);
        $historicalDataApiMock->method('fetch')->willReturn(
            json_decode(
                file_get_contents(self::JSON_RESPONSE_MINI),
                true
            )
        );
        $historicalDataApiMock->method('fetch')->willReturn(json_decode(file_get_contents(self::JSON_RESPONSE_MINI),true));

        $expectedReturnedSubset = $this->getSearchResultSubset($date_min,$date_max);

        $date_min_param = (new \DateTime())->setTimestamp($date_min);
        $date_max_param = (new \DateTime())->setTimestamp($date_max);

        $service = new SearchStockService($historicalDataApiMock,$symbolRepositoryMock);

        $result = $service->fetchData('GOOG',$date_min_param,$date_max_param);
        $this->assertNotEmpty($result);

        foreach ($result as $item){
            $this->assertLessThanOrEqual($date_max,(int)$item['date']);
            $this->assertGreaterThanOrEqual($date_min,(int)$item['date']);
        }
    }

    public function testOutOfBoundsRange()
    {
        $symbolRepositoryMock = $this->getMockBuilder(CompanySymbolRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        // We need to know for non-empty values
        $symbolRepositoryMock->method('findOneBy')->willReturn(['symbol'=>'GOOG']);

        $historicalDataApiMock = $this->createMock(HistoricalDataApi::class);
        $historicalDataApiMock->method('fetch')->willReturn(
            json_decode(
                file_get_contents(self::JSON_RESPONSE_MINI),
                true
            )
        );
        $historicalDataApiMock->method('fetch')->willReturn(json_decode(file_get_contents(self::JSON_RESPONSE_MINI),true));

        $max_date = $this->getMaxDateFromFixtures();
        // Create a range that does not exist in data

        $date_min_param = (new \DateTime())
            ->setTimestamp($max_date)
            ->setTime(0,0,0,0)
            ->modify("+1 day");

        $date_max_param = (new \DateTime())
            ->setTimestamp($max_date)
            ->setTime(23,59,59,999999)
            ->modify("+10 days");

        $service = new SearchStockService($historicalDataApiMock,$symbolRepositoryMock);

        $result = $service->fetchData('GOOG',$date_min_param,$date_max_param);

        $this->assertEmpty($result);
    }

    public function testMissingSymbol()
    {
        $symbolRepositoryMock = $this->getMockBuilder(CompanySymbolRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        // We need to know for non-empty values
        $symbolRepositoryMock->method('findOneBy')->willReturn([]);

        $historicalDataApiMock = $this->createMock(HistoricalDataApi::class);
        $historicalDataApiMock->method('fetch')->willReturn(
            json_decode(
                file_get_contents(self::JSON_RESPONSE_MINI),
                true
            )
        );
        $historicalDataApiMock->method('fetch')->willReturn(json_decode(file_get_contents(self::JSON_RESPONSE_MINI),true));

        $this->getSearchResultSubset($date_min,$date_max);

        $date_min_param = (new \DateTime())->setTimestamp($date_min);
        $date_max_param = (new \DateTime())->setTimestamp($date_max);

        $service = new SearchStockService($historicalDataApiMock,$symbolRepositoryMock);

        $this->expectException(\RuntimeException::class);
        $service->fetchData('GOOG',$date_min_param,$date_max_param);
    }

    public function testWrongProvidedRange()
    {
        $symbolRepositoryMock = $this->getMockBuilder(CompanySymbolRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy'])
            ->getMock();

        // We need to know for non-empty values
        $symbolRepositoryMock->method('findOneBy')->willReturn(['symbol'=>'GOOG']);

        $historicalDataApiMock = $this->createMock(HistoricalDataApi::class);
        $historicalDataApiMock->method('fetch')->willReturn(
            json_decode(
                file_get_contents(self::JSON_RESPONSE_MINI),
                true
            )
        );
        $historicalDataApiMock->method('fetch')->willReturn(json_decode(file_get_contents(self::JSON_RESPONSE_MINI),true));

        $this->getSearchResultSubset($date_min,$date_max);

        $date_min_param = (new \DateTime())->setTimestamp($date_min);
        $date_max_param = (new \DateTime())->setTimestamp($date_max);

        $service = new SearchStockService($historicalDataApiMock,$symbolRepositoryMock);

        $this->expectException(InvalidDateRangeException::class);
        $result = $service->fetchData('GOOG',$date_max_param,$date_min_param);

    }
}