<?php

namespace PhpTest\Dao;

use PhpTest\Infra\ConnectionCreator;
use PhpTest\Model\Auction as ModelAuction;

class Auction
{
    private $con;

    public function __construct()
    {
        $this->con = ConnectionCreator::getConnection();
    }

    public function save(ModelAuction $auction): void
    {
        $sql = 'INSERT INTO auctions (description, finished, created_at) VALUES (?, ?, ?)';
        $stm = $this->con->prepare($sql);
        $stm->bindValue(1, $auction->getDescription(), \PDO::PARAM_STR);
        $stm->bindValue(2, $auction->isFinished(), \PDO::PARAM_BOOL);
        $stm->bindValue(3, $auction->getCreatedAt()->format('Y-m-d'));
        $stm->execute();
    }

    /**
     * @return ModelAuction[]
     */
    public function recoverUnfinished(): array
    {
        return $this->retrieveAuctionsIfFinished(false);
    }

    /**
     * @return @return ModelAuction[]
     */
    public function recoverFinished(): array
    {
        return $this->retrieveAuctionsIfFinished(true);
    }

    /**
     * @return ModelAuction[]
     */
    private function retrieveAuctionsIfFinished(bool $finished): array
    {
        $sql = 'SELECT * FROM auctions WHERE finished = ' . ($finished ? 1 : 0);
        $stm = $this->con->query($sql, \PDO::FETCH_ASSOC);

        $data = $stm->fetchAll();
        $auctions = [];
        foreach ($data as $datum) {
            /** @var ModelAuction */
            $auction = new ModelAuction($datum['description'], new \DateTimeImmutable($datum['created_at']), $datum['id']);
            if ($datum['finished']) {
                $auction->finish();
            }
            $auctions[] = $auction;
        }

        return $auctions;
    }

    public function update(ModelAuction $auction)
    {
        $sql = 'UPDATE auctions SET description = :description, created_at = :created_at, finished = :finished WHERE id = :id';
        $stm = $this->con->prepare($sql);
        $stm->bindValue(':description', $auction->getDescription());
        $stm->bindValue(':created_at', $auction->getCreatedAt()->format('Y-m-d'));
        $stm->bindValue(':finished', $auction->isFinished(), \PDO::PARAM_BOOL);
        $stm->bindValue(':id', $auction->getId(), \PDO::PARAM_INT);
        $stm->execute();
    }
}