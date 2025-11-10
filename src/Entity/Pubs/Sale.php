<?php
namespace App\Entity\Pubs;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "sales")]
class Sale
{
    // --- COMPOSITE PRIMARY KEY: (stor_id, ord_num, title_id) ---
    #[ORM\Id]
    #[ORM\Column(name: "stor_id", type: "string", length: 4)]
    private string $storId;

    #[ORM\Id]
    #[ORM\Column(name: "ord_num", type: "string", length: 20)]
    private string $ordNum;

    #[ORM\Id]
    #[ORM\Column(name: "title_id", type: "string", length: 6)]
    private string $titleId;

    // --- RELATIONS (FK) ---
    #[ORM\ManyToOne(targetEntity: Store::class)]
    #[ORM\JoinColumn(name: "stor_id", referencedColumnName: "stor_id", nullable: false, onDelete: "CASCADE")]
    private ?Store $store = null;

    #[ORM\ManyToOne(targetEntity: Title::class)]
    #[ORM\JoinColumn(name: "title_id", referencedColumnName: "title_id", nullable: false, onDelete: "CASCADE")]
    private ?Title $title = null;

    // --- OTHER COLUMNS ---
    #[ORM\Column(name: "ord_date", type: "datetime_mutable")]
    private \DateTimeInterface $ordDate;

    #[ORM\Column(name: "qty", type: "smallint")]
    private int $qty;

    #[ORM\Column(name: "payterms", type: "string", length: 12)]
    private string $payterms;

    // --- getters / setters ---
    public function getStorId(): string { return $this->storId; }
    public function setStorId(string $id): self { $this->storId = $id; return $this; }

    public function getOrdNum(): string { return $this->ordNum; }
    public function setOrdNum(string $n): self { $this->ordNum = $n; return $this; }

    public function getTitleId(): string { return $this->titleId; }
    public function setTitleId(string $id): self { $this->titleId = $id; return $this; }

    public function getStore(): ?Store { return $this->store; }
    public function setStore(?Store $s): self { $this->store = $s; return $this; }

    public function getTitle(): ?Title { return $this->title; }
    public function setTitle(?Title $t): self { $this->title = $t; return $this; }

    public function getOrdDate(): \DateTimeInterface { return $this->ordDate; }
    public function setOrdDate(\DateTimeInterface $d): self { $this->ordDate = $d; return $this; }

    public function getQty(): int { return $this->qty; }
    public function setQty(int $q): self { $this->qty = $q; return $this; }

    public function getPayterms(): string { return $this->payterms; }
    public function setPayterms(string $p): self { $this->payterms = $p; return $this; }
}
