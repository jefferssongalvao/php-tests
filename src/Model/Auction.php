<?php

namespace PhpTest\Model;

use DateTimeImmutable;
use DateTimeInterface;
use DomainException;

class Auction
{
    private int $id;
    private string $description;
    private bool $isFinished;
    private DateTimeInterface $createdAt;
    /** @var AuctionBid[] */
    private $auctionBids;

    public function __construct(string $description, DateTimeImmutable $createdAt = null, ?int $id = null)
    {
        $this->description = $description;
        $this->auctionBids = [];
        $this->isFinished = false;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->id = $id ? $id : 0;
    }

    public function receiveAuctionBid(AuctionBid $auctionBid): void
    {
        $totalBidsUser = $this->totalAuctionBidsUser($auctionBid->getUser());
        if (!empty($this->auctionBids)) {
            if ($this->isFromLastUser($auctionBid)) {
                throw new DomainException("Um usuário não pode dar dois lances seguidos");
            }

            if ($totalBidsUser == 5) {
                throw new DomainException("Um usuário não pode dar mais de 5 lances");
            }
        }
        $this->auctionBids[] = $auctionBid;
    }

    public function finish(): void
    {
        $this->isFinished = true;
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

    private function isFromLastUser(AuctionBid $auctionBid): bool
    {
        $lastIdx = array_key_last($this->auctionBids);
        $lastAuctionBid = $this->auctionBids[$lastIdx];

        return $auctionBid->getUser() == $lastAuctionBid->getUser();
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

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function hasMoreThanOneWeek(): bool
    {
        $today = new \DateTime();
        $interval = $this->createdAt->diff($today);

        return $interval->days > 7;
    }
}