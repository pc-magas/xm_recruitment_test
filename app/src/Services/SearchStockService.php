<?php

namespace App\Services;

use App\Exceptions\InvalidSymbolException;

class SearchStockService
{
    private HistoricalDataApi $historicalDataApi;
    public function __construct(
        HistoricalDataApi $historicalDataApi
    )
    {
        $this->historicalDataApi = $historicalDataApi;
    }

    /**
     * @param string $symbol Company Symbol
     * @param \Datetime $from From Datetime
     * @param \Datetime $until Until Datetime
     * @return array With the stock thata that are in the criteria
     */
    public function fetchData(string $symbol,
                              \Datetime $from,
                              \Datetime $until
    ):array
    {
        $from->setTime(0,0,0,0);
        $until->setTime(23,59,59,999999);

        $from_unix_time=$from->getTimestamp();
        $until_unix_time=$until->getTimestamp();

        if($from_unix_time>$until_unix_time){
            throw new \InvalidArgumentException('The date range in not valid');
        }

        try {
            $result = $this->historicalDataApi->fetch($symbol);
        } catch (\Exception $e){
            throw $e;
        }

        if(empty($result['prices'])){
            return [];
        }



        return [];
    }

}