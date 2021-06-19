<?php

namespace PhpTest\tests\Service;

use DateTimeImmutable;
use PhpTest\Dao\Auction as DaoAuction;
use PhpTest\Model\Auction;
use PhpTest\Service\Finisher;
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

        $auctionDao = $this->createMock(DaoAuction::class);
        $auctionDao->method("recoverUnfinished")
            ->willReturn([$auction1, $auction2]);


        /** @var DaoAuction */
        $auctionDao = $auctionDao;

        $finisher = new Finisher($auctionDao);
        $finisher->finish();

        $auctionsFinished = [$auction1, $auction2];
        self::assertCount(2, $auctionsFinished);
        self::assertTrue($auctionsFinished[0]->isFinished());
        self::assertTrue($auctionsFinished[1]->isFinished());
    }
}