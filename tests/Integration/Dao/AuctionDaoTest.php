<?php

namespace PhpTest\Tests\Integration\Dao;

use PDO;
use PhpTest\Dao\Auction as DaoAuction;
use PhpTest\Model\Auction;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertFalse;

class AuctionDaoTest extends TestCase
{
    private static PDO $pdo;
    private DaoAuction $auctionDao;
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
        $this->auctionDao =  new DaoAuction(self::$pdo);
    }

    /**
     *
     * @dataProvider getAuctions
     * @param Auction[] $auctions
     */
    public function testSearchUnfinishedAuctions(array $auctions): void
    {
        $this->saveAuctions($auctions);
        $auctions = $this->auctionDao->recoverUnfinished();

        static::assertCount(1, $auctions);
        static::assertContainsOnlyInstancesOf(Auction::class, $auctions);
        static::assertSame("Auction Test Unfinished", $auctions[0]->getDescription());
        static::assertFalse($auctions[0]->isFinished());
    }

    /**
     *
     * @dataProvider getAuctions
     * @param Auction[] $auctions
     */
    public function testWhenUpdatingAuctionTheStatusMustBeChanged(array $auctions): void
    {
        $auction = $auctions[0];
        $auction = $this->auctionDao->save($auction);

        $auctions = $this->auctionDao->recoverUnfinished();
        static::assertCount(1, $auctions);
        static::assertSame("Auction Test Unfinished", $auctions[0]->getDescription());
        static::assertFalse($auctions[0]->isFinished());

        $auction->finish();
        $this->auctionDao->update($auction);

        $auctions = $this->auctionDao->recoverFinished();
        static::assertCount(1, $auctions);
        static::assertSame("Auction Test Unfinished", $auctions[0]->getDescription());
        static::assertTrue($auctions[0]->isFinished());
    }

    /**
     *
     * @dataProvider getAuctions
     * @param Auction[] $auctions
     */
    public function testSearchFinishedAuctions(array $auctions): void
    {
        $this->saveAuctions($auctions);
        $auctions = $this->auctionDao->recoverFinished();

        static::assertCount(1, $auctions);
        static::assertContainsOnlyInstancesOf(Auction::class, $auctions);
        static::assertSame("Auction Test Finished", $auctions[0]->getDescription());
        static::assertTrue($auctions[0]->isFinished());
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }

    public function getAuctions(): array
    {
        $unfinished = new Auction("Auction Test Unfinished");
        $finished = new Auction("Auction Test Finished");
        $finished->finish();

        return [
            [
                [$unfinished, $finished]
            ]
        ];
    }

    private function saveAuctions(array $auctions): void
    {
        foreach ($auctions as $auction) {
            $this->auctionDao->save($auction);
        }
    }
}