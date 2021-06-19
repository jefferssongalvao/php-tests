<?php

namespace PhpTest\Service;

use DomainException;
use PhpTest\Dao\Auction;

class Finisher
{
    private Auction $auctionDao;
    private SenderMail $senderMail;

    public function __construct(Auction $auctionDao, SenderMail $senderMail)
    {
        $this->auctionDao = $auctionDao;
        $this->senderMail = $senderMail;
    }

    public function finish()
    {
        $auctions = $this->auctionDao->recoverUnfinished();

        foreach ($auctions as $auction) {
            if ($auction->hasMoreThanOneWeek()) {
                try {
                    $auction->finish();
                    $this->auctionDao->update($auction);
                    $this->senderMail->notifyAuctionFinishing($auction);
                } catch (DomainException $e) {
                    error_log($e->getMessage());
                }
            }
        }
    }
}