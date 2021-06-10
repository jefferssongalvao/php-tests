<?php

namespace PhpTest\tests\Model;

use PhpTest\Model\Auction;
use PhpTest\Model\AuctionBid;
use PhpTest\Model\User;
use PHPUnit\Framework\TestCase;

class AuctionTest extends TestCase
{

    public function testNotReceiveFiveBidsPerUser(): void
    {
        $auction = new Auction("Auction Test");

        $user1 = new User("Maria");
        $user2 = new User("José");

        $auction->receiveAuctionBid(new AuctionBid($user1, 1000));
        $auction->receiveAuctionBid(new AuctionBid($user2, 1500));
        $auction->receiveAuctionBid(new AuctionBid($user1, 2000));
        $auction->receiveAuctionBid(new AuctionBid($user2, 2500));
        $auction->receiveAuctionBid(new AuctionBid($user1, 3000));
        $auction->receiveAuctionBid(new AuctionBid($user2, 3500));
        $auction->receiveAuctionBid(new AuctionBid($user1, 4000));
        $auction->receiveAuctionBid(new AuctionBid($user2, 4500));
        $auction->receiveAuctionBid(new AuctionBid($user1, 5000));
        $auction->receiveAuctionBid(new AuctionBid($user2, 5500));

        $auction->receiveAuctionBid(new AuctionBid($user1, 6000));

        $lastIdx = array_key_last($auction->getAuctionBids());
        
        self::assertCount(10, $auction->getAuctionBids());
        self::assertEquals(5500, $auction->getAuctionBids()[$lastIdx]->getValue());

    }

    public function testNotReceiveRepeatBid(): void
    {
        $auction = new Auction("Auction Text");

        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 1000));
        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 5000));

        $auctionBids = $auction->getAuctionBids();

        self::assertCount(1, $auctionBids);
        self::assertEquals(1000, $auctionBids[0]->getValue());
    }    

    /**
     * @dataProvider generateBids
     */
    public function testAuctionMustReceiveBids(
        int $numberOfBids,
        Auction $auction,
        array $values ): void
    {
        self::assertInstanceOf(Auction::class, $auction);
        self::assertCount($numberOfBids, $auction->getAuctionBids());
        foreach($values as $idx => $value)
            self::assertEquals($value, $auction->getAuctionBids()[$idx]->getValue());
    }

    public function generateBids(): array
    {
        $auctionOneBid = new Auction("Auction Text");
        $auctionOneBid->receiveAuctionBid(new AuctionBid(new User("Maria"), 1700));

        $auctionTwoBids = new Auction("Auction Text");
        $auctionTwoBids->receiveAuctionBid(new AuctionBid(new User("Maria"), 1700));
        $auctionTwoBids->receiveAuctionBid(new AuctionBid(new User("José"), 2000));

        return [
            "one-bid" => [1, $auctionOneBid, [1700]],
            "two-bid" => [2, $auctionTwoBids, [1700, 2000]],
        ];
    }
}