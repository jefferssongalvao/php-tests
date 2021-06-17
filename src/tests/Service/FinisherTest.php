<?php

namespace PhpTest\tests\Service;

use DateTimeImmutable;
use PhpTest\Model\Auction;
use PhpTest\Service\Finisher;
use PhpTest\tests\Service\MockTest\AuctionDaoMock;
use PHPUnit\Framework\TestCase;

class FinisherTest extends TestCase
{
    public function testAuctionMoreThanOneWeekMustBeFinished(): void
    {

        $auction1 = new Auction(
            "Auction Test 1",
            new DateTimeImmutable("8 days ago")
        );
        $auction2 = new Auction(
            "Auction Test 2",
            new DateTimeImmutable("10 days ago")
        );

        $auctionDao = new AuctionDaoMock();
        $auctionDao->save($auction1);
        $auctionDao->save($auction2);

        $finisher = new Finisher($auctionDao);
        $finisher->finish();

        $auctionsFinished = $auctionDao->recoverFinished();

        self::assertCount(2, $auctionsFinished);
    }
}