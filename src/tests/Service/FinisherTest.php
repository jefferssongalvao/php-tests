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
        $threeBedroomHouse = new Auction(
            "Three Bedroom House",
            new DateTimeImmutable("8 days ago")
        );
        $bigApartment = new Auction(
            "Big Apartment",
            new DateTimeImmutable("10 days ago")
        );

        $auctionDao = $this->createMock(DaoAuction::class);
        $auctionDao->method("recoverUnfinished")
            ->willReturn([$threeBedroomHouse, $bigApartment]);


        /** @var DaoAuction */
        $auctionDao = $auctionDao;

        $finisher = new Finisher($auctionDao);
        $finisher->finish();

        $auctionsFinished = [$threeBedroomHouse, $bigApartment];
        self::assertCount(2, $auctionsFinished);
        self::assertTrue($auctionsFinished[0]->isFinished());
        self::assertTrue($auctionsFinished[1]->isFinished());
    }
}