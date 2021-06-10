<?php

namespace PhpTest\Service;

use PhpTest\Model\Auction;
use PhpTest\Model\AuctionBid;

class Evaluator
{
    private float $highestValue;
    private float $lowerValue;
    /** @var AuctionBid[] */
    private array $highestAuctionBids;

    public function __construct()
    {
        $this->highestValue = -INF;
        $this->lowerValue = INF;
        $this->highestAuctionBids = [];
    }
    
    public function evaluate(Auction $auction): void
    {
        $auctionBids = $auction->getAuctionBids();
        foreach($auctionBids as $auctionBid){
            $actualValue = $auctionBid->getValue();
            if($actualValue > $this->highestValue)
                $this->highestValue = $actualValue;
            if($actualValue < $this->lowerValue)
                $this->lowerValue = $actualValue;
        }
        usort($auctionBids, fn(AuctionBid $auctionBid1, AuctionBid $auctionBid2) => $auctionBid2->getValue() - $auctionBid1->getValue());

        $this->highestAuctionBids = array_slice($auctionBids, 0, 3);
    }

    public function getHighestValue(): float
    {
        return $this->highestValue;
    }
    
    public function getLowerValue(): float
    {
        return $this->lowerValue;
    }
    
    /**
     *
     * @return AuctionBid[]
     */
    public function getHighestAuctionBids(): array
    {
        return $this->highestAuctionBids;
    }
}