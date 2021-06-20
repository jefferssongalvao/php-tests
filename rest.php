<?php

use PhpTest\Dao\Auction;
use PhpTest\Model\Auction as ModelAuction;

require_once __DIR__ . '/vendor/autoload.php';

$pdo = new \PDO('sqlite::memory:');
$pdo->exec('create table auctions (
    id INTEGER primary key,
    description TEXT,
    finished BOOL,
    created_at TEXT
);');

$auctionDao = new Auction($pdo);

$auction1 = new ModelAuction('Auction 1');
$auction2 = new ModelAuction('Auction 2');
$auction3 = new ModelAuction('Auction 3');
$auction4 = new ModelAuction('Auction 4');

$auctionDao->save($auction1);
$auctionDao->save($auction2);
$auctionDao->save($auction3);
$auctionDao->save($auction4);

header('Content-type: application/json');
echo json_encode(array_map(function (ModelAuction $auction) {
    return [
        'description' => $auction->getDescription(),
        'finished' => $auction->isFinished(),
    ];
}, $auctionDao->recoverUnfinished()));