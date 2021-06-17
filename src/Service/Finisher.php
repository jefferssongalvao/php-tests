<?php

namespace PhpTest\Service;

use PhpTest\Dao\Auction;

class Finisher
{
    private Auction $auctionDao;

    public function __construct(Auction $auctionDao)
    {
        $this->auctionDao = $auctionDao;
    }

    public function finish()
    {
        $auctions = $this->auctionDao->recoverUnfinished();
        foreach ($auctions as $auction) {
            if ($auction->hasMoreThanOneWeek()) {
                $auction->finish();
                $this->auctionDao->update($auction);
            }
        }
    }
}