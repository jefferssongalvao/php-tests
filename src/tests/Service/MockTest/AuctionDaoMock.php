<?php

namespace PhpTest\tests\Service\MockTest;

use PhpTest\Dao\Auction;
use PhpTest\Model\Auction as ModelAuction;

class AuctionDaoMock extends Auction
{
    /** @var ModelAuction[] */
    private $auctions = [];

    public function save(ModelAuction $auction): void
    {
        $this->auctions[] = $auction;
    }

    /**
     * @return ModelAuction[]
     */
    public function recoverFinished(): array
    {
        return array_filter($this->auctions, fn (ModelAuction $auction) => $auction->isFinished());
    }

    /**
     * @return ModelAuction[]
     */

    public function recoverUnfinished(): array
    {
        return array_filter($this->auctions, fn (ModelAuction $auction) => !$auction->isFinished());
    }

    public function update(ModelAuction $auction)
    {
    }
}