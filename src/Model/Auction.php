<?php

namespace PhpTest\Model;

class Auction
{
    /** @var AuctionBid[] */
    private $auctionBids;
    private string $description;

    public function __construct(string $description)
    {
        $this->description = $description;
        $this->auctionBids = [];
    }

    public function receiveAuctionBid(AuctionBid $auctionBid): void
    {
        $totalBidsUser = $this->totalAuctionBidsUser($auctionBid->getUser());
        if (empty($this->auctionBids) || ($this->isNotLastUser($auctionBid) && $totalBidsUser < 5))
            $this->auctionBids[] = $auctionBid;
    }

    /**
     *
     * @return AuctionBid[]
     */
    public function getAuctionBids(): array
    {
        return $this->auctionBids;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    private function isNotLastUser(AuctionBid $auctionBid): bool
    {
        $lastIdx = array_key_last($this->auctionBids);
        $lastAuctionBid = $this->auctionBids[$lastIdx];

        return $auctionBid->getUser() != $lastAuctionBid->getUser();
    }

    private function totalAuctionBidsUser(User $user): int
    {
        return array_reduce(
            $this->auctionBids,
            function (int $total, AuctionBid $actualAuctionBid) use ($user) {
                if ($actualAuctionBid->getUser() == $user)
                    return $total + 1;
                return $total;
            },
            0
        );
    }
}