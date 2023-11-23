<?php

namespace App\Services;

use App\Exceptions\InvalidDateRangeException;
use App\Exceptions\InvalidSymbolException;
use App\Repository\CompanySymbolRepository;

class SearchStockService
{
    private HistoricalDataApi $historicalDataApi;
    private CompanySymbolRepository $symbolRepository;

    public function __construct(
        HistoricalDataApi $historicalDataApi,
        CompanySymbolRepository $symbolRepository
    )
    {
        $this->historicalDataApi = $historicalDataApi;
        $this->symbolRepository=$symbolRepository;
    }

    /**
     * @param string $symbol Company Symbol
     * @param \Datetime $from From Datetime
     * @param \Datetime $until Until Datetime
     * @return array With the stock thata that are in the criteria
     *
     * @throws \RuntimeException
     * @throws InvalidDateRangeException
     * @throws InvalidSymbolException
     */
    public function fetchData(string $symbol,
                              \Datetime $from,
                              \Datetime $until
    ):array
    {
        // we need to search for a whole day
        $from->setTime(0,0,0,0);
        $until->setTime(23,59,59,999999);

        $from_unix_time=$from->getTimestamp();
        $until_unix_time=$until->getTimestamp();

        $symbol=strtoupper(trim($symbol));

        if(empty($symbol)){
            throw new InvalidSymbolException();
        }

        if(empty($this->symbolRepository->findOneBy(['symbol'=>$symbol]))){
            throw new \RuntimeException("Symbol is not found");
        }

        if($from_unix_time>$until_unix_time){
            throw new InvalidDateRangeException();
        }

        $result = $this->historicalDataApi->fetch($symbol);

        if(empty($result['prices'])){
            return [];
        }

        $endresult=[];
        foreach ($result['prices'] as $price){
            $date_unix = (int)$price['date'];
            if($date_unix >= $from_unix_time && $date_unix <= $until_unix_time){
                $endresult[] = $price;
            }
        }

        return $endresult;
    }

}