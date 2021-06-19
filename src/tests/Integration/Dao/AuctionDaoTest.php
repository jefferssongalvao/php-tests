<?php

namespace PhpTest\tests\Integration\Dao;

use PDO;
use PhpTest\Dao\Auction as DaoAuction;
use PhpTest\Model\Auction;
use PHPUnit\Framework\TestCase;

class AuctionDaoTest extends TestCase
{
    private static PDO $pdo;
    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO("sqlite::memory:");
        self::$pdo->exec("
            create table auctions (
                id INTEGER primary key,
                description TEXT,
                created_at TEXT,
                finished BOOL
            );");
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    public function testInsertionAndSearchShouldWork(): void
    {
        $auction = new Auction("Auction Test");
        $auctionDao =  new DaoAuction(self::$pdo);

        $auctionDao->save($auction);
        $auctions = $auctionDao->recoverUnfinished();

        static::assertCount(1, $auctions);
        static::assertContainsOnlyInstancesOf(Auction::class, $auctions);
        static::assertSame("Auction Test", $auctions[0]->getDescription());
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}