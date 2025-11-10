<?php
namespace App\Entity\Pubs;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "titleauthor")]
class TitleAuthor
{
    // COMPOSITE KEY: au_id + title_id

    #[ORM\Id]
    #[ORM\Column(name: "au_id", type: "string", length: 11)]
    private string $auId;

    #[ORM\Id]
    #[ORM\Column(name: "title_id", type: "string", length: 6)]
    private string $titleId;

    // İlişkiler (FK) – tablo zaten var, migration çalıştırmayacağız.
    #[ORM\ManyToOne(targetEntity: Author::class)]
    #[ORM\JoinColumn(name: "au_id", referencedColumnName: "au_id", nullable: false, onDelete: "CASCADE")]
    private ?Author $author = null;

    #[ORM\ManyToOne(targetEntity: Title::class)]
    #[ORM\JoinColumn(name: "title_id", referencedColumnName: "title_id", nullable: false, onDelete: "CASCADE")]
    private ?Title $title = null;

    // MSSQL tinyint → Doctrine smallint
    #[ORM\Column(name: "au_ord", type: "smallint", nullable: true)]
    private ?int $auOrd = null;

    #[ORM\Column(name: "royaltyper", type: "integer", nullable: true)]
    private ?int $royaltyper = null;

    // ---- getters / setters ----
    public function getAuId(): string { return $this->auId; }
    public function setAuId(string $id): self { $this->auId = $id; return $this; }

    public function getTitleId(): string { return $this->titleId; }
    public function setTitleId(string $id): self { $this->titleId = $id; return $this; }

    public function getAuthor(): ?Author { return $this->author; }
    public function setAuthor(?Author $a): self { $this->author = $a; return $this; }

    public function getTitle(): ?Title { return $this->title; }
    public function setTitle(?Title $t): self { $this->title = $t; return $this; }

    public function getAuOrd(): ?int { return $this->auOrd; }
    public function setAuOrd(?int $ord): self { $this->auOrd = $ord; return $this; }

    public function getRoyaltyper(): ?int { return $this->royaltyper; }
    public function setRoyaltyper(?int $r): self { $this->royaltyper = $r; return $this; }
}
