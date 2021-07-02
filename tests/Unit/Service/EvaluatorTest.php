<?php

namespace PhpTest\Tests\Unit\Service;

use DomainException;
use PhpTest\Model\Auction;
use PhpTest\Model\AuctionBid;
use PhpTest\Model\User;
use PhpTest\Service\Evaluator;
use PHPUnit\Framework\TestCase;

class EvaluatorTest extends TestCase
{
    private Evaluator $evaluator;
    protected function setUp(): void
    {
        $this->evaluator = new Evaluator();
    }

    public function testAuctionFinishedNotEvaluated(): void
    {
        $this->expectException(DomainException::class);

        $auction = new Auction("Auction Finished");
        $auction->finish();

        $this->evaluator->evaluate($auction);
    }

    public function testEmptyAuctionCannotEvaluated(): void
    {
        $this->expectException(DomainException::class);

        $auction = new Auction("Auction Empty");
        $this->evaluator->evaluate($auction);
    }

    /**
     * @dataProvider getAuctions
     */
    public function testHighestValueInAuction(Auction $auction): void
    {
        $this->evaluator->evaluate($auction);

        self::assertEquals(3500, $this->evaluator->getHighestValue());
    }

    /**
     * @dataProvider getAuctions
     */
    public function testLowerValueInAuction(Auction $auction): void
    {
        $this->evaluator->evaluate($auction);

        self::assertEquals(1700, $this->evaluator->getLowerValue());
    }

    /**
     * @dataProvider getAuctions
     */
    public function testHighestAuctionBidsInAuction(Auction $auction): void
    {
        $this->evaluator->evaluate($auction);

        $highestAuctionBids = $this->evaluator->getHighestAuctionBids();
        self::assertIsArray($highestAuctionBids);
        self::assertCount(3, $highestAuctionBids);
        self::assertEquals(3500, $highestAuctionBids[0]->getValue());
        self::assertEquals(2500, $highestAuctionBids[1]->getValue());
        self::assertEquals(2000, $highestAuctionBids[2]->getValue());
    }

    public function getAuctions(): array
    {
        return [
            "ascendant-order" => $this->auctionAsc(),
            "descendant-order" => $this->auctionDesc(),
            "random-order" => $this->auctionRand()
        ];
    }

    private function auctionAsc(): array
    {
        $auction = new Auction("Auction Text");
        $auction->receiveAuctionBid(new AuctionBid(new User("Mary"), 1700));
        $auction->receiveAuctionBid(new AuctionBid(new User("Joseph"), 2000));
        $auction->receiveAuctionBid(new AuctionBid(new User("John"), 2500));
        $auction->receiveAuctionBid(new AuctionBid(new User("Mary"), 3500));
        return [$auction];
    }

    private function auctionDesc(): array
    {
        $auction = new Auction("Auction Text");
        $auction->receiveAuctionBid(new AuctionBid(new User("Mary"), 3500));
        $auction->receiveAuctionBid(new AuctionBid(new User("John"), 2500));
        $auction->receiveAuctionBid(new AuctionBid(new User("Joseph"), 2000));
        $auction->receiveAuctionBid(new AuctionBid(new User("Mary"), 1700));
        return [$auction];
    }

    private function auctionRand(): array
    {
        $auction = new Auction("Auction Text");
        $auction->receiveAuctionBid(new AuctionBid(new User("Joseph"), 2000));
        $auction->receiveAuctionBid(new AuctionBid(new User("Mary"), 3500));
        $auction->receiveAuctionBid(new AuctionBid(new User("John"), 2500));
        $auction->receiveAuctionBid(new AuctionBid(new User("Mary"), 1700));
        return [$auction];
    }
}