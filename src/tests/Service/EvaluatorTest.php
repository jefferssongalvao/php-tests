<?php

namespace PhpTest\tests\Service;

use PhpTest\Model\Auction;
use PhpTest\Model\AuctionBid;
use PhpTest\Model\User;
use PhpTest\Service\Evaluator;
use PHPUnit\Framework\TestCase;

class EvaluatorTest extends TestCase
{
    public function testHighestValueInAscOrder(): void
    {
        $auction = new Auction("Leilão de Teste");
        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 2000));
        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 2500));

        $evaluator = new Evaluator();
        $evaluator->evaluate($auction);

        self::assertEquals(2500, $evaluator->getHighestValue());
    }

    public function testHighestValueInDescOrder(): void
    {
        $auction = new Auction("Leilão de Teste");
        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 2500));
        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 2000));

        $evaluator = new Evaluator();
        $evaluator->evaluate($auction);

        self::assertEquals(2500, $evaluator->getHighestValue());
    }

    public function testLowerValueInAscOrder(): void
    {
        $auction = new Auction("Leilão de Teste");
        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 2000));
        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 2500));

        $evaluator = new Evaluator();
        $evaluator->evaluate($auction);

        self::assertEquals(2000, $evaluator->getLowerValue());
    }

    public function testLowerValueInDescOrder(): void
    {
        $auction = new Auction("Leilão de Teste");
        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 2000));
        $auction->receiveAuctionBid(new AuctionBid(new User("Maria"), 2500));

        $evaluator = new Evaluator();
        $evaluator->evaluate($auction);

        self::assertEquals(2000, $evaluator->getLowerValue());
    }
}