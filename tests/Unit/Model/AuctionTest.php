<?php

namespace PhpTest\Tests\Unit\Model;

use DomainException;
use PhpTest\Model\Auction;
use PhpTest\Model\AuctionBid;
use PhpTest\Model\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuctionTest extends TestCase
{
    /** @var MockObject */
    private User $maryUser;
    /** @var MockObject */
    private User $josephUser;

    protected function setUp(): void
    {
        $this->maryUser = $this->getMockBuilder(User::class)->setConstructorArgs(["Mary"])->getMock();
        $this->josephUser = $this->getMockBuilder(User::class)->setConstructorArgs(["Joseph"])->getMock();
    }


    public function testNotReceiveFiveBidsPerUser(): void
    {
        $this->expectException(DomainException::class);

        $auction = new Auction("Auction Test");

        $auction->receiveAuctionBid(new AuctionBid($this->maryUser, 1000));
        $auction->receiveAuctionBid(new AuctionBid($this->josephUser, 1500));
        $auction->receiveAuctionBid(new AuctionBid($this->maryUser, 2000));
        $auction->receiveAuctionBid(new AuctionBid($this->josephUser, 2500));
        $auction->receiveAuctionBid(new AuctionBid($this->maryUser, 3000));
        $auction->receiveAuctionBid(new AuctionBid($this->josephUser, 3500));
        $auction->receiveAuctionBid(new AuctionBid($this->maryUser, 4000));
        $auction->receiveAuctionBid(new AuctionBid($this->josephUser, 4500));
        $auction->receiveAuctionBid(new AuctionBid($this->maryUser, 5000));
        $auction->receiveAuctionBid(new AuctionBid($this->josephUser, 5500));

        $auction->receiveAuctionBid(new AuctionBid($this->maryUser, 6000));
    }

    public function testNotReceiveRepeatBid(): void
    {
        $this->expectException(DomainException::class);
        $auction = new Auction("Auction Text");

        $auction->receiveAuctionBid(new AuctionBid($this->maryUser, 1000));
        $auction->receiveAuctionBid(new AuctionBid($this->maryUser, 5000));

        $auctionBids = $auction->getAuctionBids();
    }

    /**
     * @dataProvider generateBids
     */
    public function testAuctionMustReceiveBids(
        int $numberOfBids,
        Auction $auction,
        array $values
    ): void {
        self::assertInstanceOf(Auction::class, $auction);
        self::assertCount($numberOfBids, $auction->getAuctionBids());
        foreach ($values as $idx => $value)
            self::assertEquals($value, $auction->getAuctionBids()[$idx]->getValue());
    }

    public function generateBids(): array
    {
        /** @var User */
        $maryUser = $this->getMockBuilder(User::class)->setConstructorArgs(["Mary"])->getMock();
        /** @var User */
        $josephUser = $this->getMockBuilder(User::class)->setConstructorArgs(["Joseph"])->getMock();

        $auctionOneBid = new Auction("Auction Text");

        $auctionOneBid->receiveAuctionBid(new AuctionBid($maryUser, 1700));

        $auctionTwoBids = new Auction("Auction Text");
        $auctionTwoBids->receiveAuctionBid(new AuctionBid($maryUser, 1700));
        $auctionTwoBids->receiveAuctionBid(new AuctionBid($josephUser, 2000));

        return [
            "one-bid" => [1, $auctionOneBid, [1700]],
            "two-bid" => [2, $auctionTwoBids, [1700, 2000]],
        ];
    }
}