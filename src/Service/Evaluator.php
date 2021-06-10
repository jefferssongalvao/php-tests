<?php

namespace PhpTest\Service;

use PhpTest\Model\Auction;

class Evaluator
{
    private float $highestValue;
    private float $lowerValue;
    public function __construct()
    {
        $this->highestValue = -INF;
        $this->lowerValue = INF;
    }
    public function evaluate(Auction $auction): void
    {
        foreach($auction->getAuctionBids() as $auctionBid){
            $actualValue = $auctionBid->getValue();
            if($actualValue > $this->highestValue)
                $this->highestValue = $actualValue;
            if($actualValue < $this->lowerValue)
                $this->lowerValue = $actualValue;
        }
    }

    public function getHighestValue(): float
    {
        return $this->highestValue;
    }
    
    public function getLowerValue(): float
    {
        return $this->lowerValue;
    }
}