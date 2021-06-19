<?php

namespace PhpTest\Service;

use DomainException;
use PhpTest\Model\Auction;

class SenderMail
{
    public function notifyAuctionFinishing(Auction $auction): void
    {
        $emailResult = mail(
            "usuer@email.com",
            "Auction Finished",
            "Auction $auction->getDescription() finished"
        );

        if (!$emailResult) {
            throw new DomainException("Failed to send email");
        }
    }
}