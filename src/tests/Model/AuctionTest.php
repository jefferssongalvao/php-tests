<?php

namespace PhpTest\tests\Model;

use DomainException;
use PhpTest\Model\Auction;
use PhpTest\Model\AuctionBid;
use PhpTest\Model\User;
use PHPUnit\Framework\TestCase;

class AuctionTest extends TestCase
{

    public function testNotReceiveFiveBidsPerUser(): void
    {
        $this->expectException(DomainException::class);

        $auction = new Auction("Auction Test");

        $maryUser = new User("Mary");
        $joseph = new User("Joseph");

        $auction->receiveAuctionBid(new AuctionBid($maryUser, 1000));
        $auction->receiveAuctionBid(new AuctionBid($joseph, 1500));
        $auction->receiveAuctionBid(new AuctionBid($maryUser, 2000));
        $auction->receiveAuctionBid(new AuctionBid($joseph, 2500));
        $auction->receiveAuctionBid(new AuctionBid($maryUser, 3000));
        $auction->receiveAuctionBid(new AuctionBid($joseph, 3500));
        $auction->receiveAuctionBid(new AuctionBid($maryUser, 4000));
        $auction->receiveAuctionBid(new AuctionBid($joseph, 4500));
        $auction->receiveAuctionBid(new AuctionBid($maryUser, 5000));
        $auction->receiveAuctionBid(new AuctionBid($joseph, 5500));

        $auction->receiveAuctionBid(new AuctionBid($maryUser, 6000));
    }

    public function testNotReceiveRepeatBid(): void
    {
        $this->expectException(DomainException::class);
        $auction = new Auction("Auction Text");

        $auction->receiveAuctionBid(new AuctionBid(new User("Mary"), 1000));
        $auction->receiveAuctionBid(new AuctionBid(new User("Mary"), 5000));

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
        $auctionOneBid = new Auction("Auction Text");
        $auctionOneBid->receiveAuctionBid(new AuctionBid(new User("Mary"), 1700));

        $auctionTwoBids = new Auction("Auction Text");
        $auctionTwoBids->receiveAuctionBid(new AuctionBid(new User("Mary"), 1700));
        $auctionTwoBids->receiveAuctionBid(new AuctionBid(new User("Joseph"), 2000));

        return [
            "one-bid" => [1, $auctionOneBid, [1700]],
            "two-bid" => [2, $auctionTwoBids, [1700, 2000]],
        ];
    }
}