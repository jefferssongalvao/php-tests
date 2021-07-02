<?php

namespace PhpTest\Tests\Unit\Service;

use DateTimeImmutable;
use DomainException;
use PhpTest\Dao\Auction as DaoAuction;
use PhpTest\Model\Auction;
use PhpTest\Service\Finisher;
use PhpTest\Service\SenderMail;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FinisherTest extends TestCase
{
    private Finisher $finisher;
    /** @var MockObject */
    private SenderMail $senderMail;
    private Auction $threeBedroomHouse;
    private Auction $bigApartment;

    protected function setUp(): void
    {
        $this->threeBedroomHouse = new Auction(
            "Three Bedroom House",
            new DateTimeImmutable("8 days ago")
        );
        $this->bigApartment = new Auction(
            "Big Apartment",
            new DateTimeImmutable("10 days ago")
        );

        /** @var MockObject */
        $auctionDao = $this->createMock(DaoAuction::class);
        $auctionDao->method("recoverUnfinished")
            ->willReturn([$this->threeBedroomHouse, $this->bigApartment]);


        /** @var DaoAuction */
        $auctionDao = $auctionDao;
        $this->senderMail = $this->createMock(SenderMail::class);

        $this->finisher = new Finisher($auctionDao, $this->senderMail);
    }

    public function testAuctionMoreThanOneWeekMustBeFinished(): void
    {
        $this->finisher->finish();
        $auctionsFinished = [$this->threeBedroomHouse, $this->bigApartment];

        static::assertCount(2, $auctionsFinished);
        static::assertTrue($auctionsFinished[0]->isFinished());
        static::assertTrue($auctionsFinished[1]->isFinished());
    }

    public function testMustContinueToSendEmailWhenItEncountersSendingFailure(): void
    {
        $this->senderMail->expects($this->exactly(2))
            ->method("notifyAuctionFinishing")
            ->willThrowException(new DomainException("Failed to send email"));

        $this->finisher->finish();
    }

    public function shouldOnlySendAuctionAfterFinished(): void
    {
        $this->senderMail->expects($this->exactly(2))
            ->method("notifyAuctionFinishing")
            ->willReturnCallback(
                fn (Auction $auction) => static::assertTrue($auction->isFinished())
            );
        $this->finisher->finish();
    }
}