<?php

namespace App\DataSerialization;

use App\Entity\CompanySymbol;

class SearchResult
{
    private CompanySymbol $symbol;
    private $prices=[];
    public function __construct(CompanySymbol $symbol,array $prices){
        $this->prices=$prices;
        $this->symbol=$symbol;
    }

    public function getPrices(): array
    {
        return $this->prices;
    }

    public function getSymbol(): CompanySymbol
    {
        return $this->symbol;
    }
}