<?php

namespace PhpTest\Model;

class Auction
{
    /** @var AuctionBid[] */
    private $auctionBids;
    private string $description;

    public function __construct(string $description)
    {
        $this->description = $description;
        $this->auctionBids = [];
    }

    public function receiveAuctionBid(AuctionBid $auctionBid): void
    {
        $this->auctionBids[] = $auctionBid;
    }
    
    /**
     *
     * @return AuctionBid[]
     */
    public function getAuctionBids(): array
    {
        return $this->auctionBids;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}