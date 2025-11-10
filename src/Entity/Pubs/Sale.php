<?php

namespace App\Entity\Pubs;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "sales")]
class Sale
{
    // --- COMPOSITE PRIMARY KEY (via relations + scalar) ---
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Store::class)]
    #[ORM\JoinColumn(name: "stor_id", referencedColumnName: "stor_id", nullable: false, onDelete: "CASCADE")]
    private Store $store;

    #[ORM\Id]
    #[ORM\Column(name: "ord_num", type: "string", length: 20)]
    private string $ordNum;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Title::class)]
    #[ORM\JoinColumn(name: "title_id", referencedColumnName: "title_id", nullable: false, onDelete: "CASCADE")]
    private Title $title;

    // --- OTHER COLUMNS ---
    #[ORM\Column(name: "ord_date", type: "datetime")]
    private \DateTimeInterface $ordDate;

    #[ORM\Column(name: "qty", type: "smallint")]
    private int $qty;

    #[ORM\Column(name: "payterms", type: "string", length: 12)]
    private string $payterms;

    // --- GETTERS / SETTERS ---
    public function getStore(): Store
    {
        return $this->store;
    }
    public function setStore(Store $store): self
    {
        $this->store = $store;
        return $this;
    }

    public function getOrdNum(): string
    {
        return $this->ordNum;
    }
    public function setOrdNum(string $ordNum): self
    {
        $this->ordNum = $ordNum;
        return $this;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }
    public function setTitle(Title $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getOrdDate(): \DateTimeInterface
    {
        return $this->ordDate;
    }
    public function setOrdDate(\DateTimeInterface $ordDate): self
    {
        $this->ordDate = $ordDate;
        return $this;
    }

    public function getQty(): int
    {
        return $this->qty;
    }
    public function setQty(int $qty): self
    {
        $this->qty = $qty;
        return $this;
    }

    public function getPayterms(): string
    {
        return $this->payterms;
    }
    public function setPayterms(string $payterms): self
    {
        $this->payterms = $payterms;
        return $this;
    }


    // --- SHORTCUTS FOR COMPOSITE KEY ACCESS ---
    public function getStorId(): string
    {
        return $this->store->getId();
    }

    public function getTitleId(): string
    {
        return $this->title->getId();
    }
}
