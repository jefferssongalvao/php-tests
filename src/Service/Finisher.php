<?php

namespace PhpTest\Service;

use PhpTest\Dao\Auction;

class Finisher
{
    public function finish()
    {
        $dao = new Auction();
        $auctions = $dao->recoverUnfinished();

        foreach ($auctions as $auction) {
            if ($auction->hasMoreThanOneWeek()) {
                $auction->finish();
                $dao->update($auction);
            }
        }
    }
}