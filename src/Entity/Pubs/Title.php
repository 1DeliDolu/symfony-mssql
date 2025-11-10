<?php
namespace App\Entity\Pubs;

use App\Repository\TitleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TitleRepository::class)]
#[ORM\Table(name: "titles")]
class Title
{
    #[ORM\Id]
    #[ORM\Column(name: "title_id", type: "string", length: 6)]
    private string $id;

    #[ORM\Column(name: "title", type: "string", length: 80)]
    private string $title;

    #[ORM\Column(name: "type", type: "string", length: 12, options: ["default" => "UNDECIDED"])]
    private string $type = 'UNDECIDED';

    #[ORM\Column(name: "pub_id", type: "string", length: 4, nullable: true)]
    private ?string $pubId = null;

    #[ORM\Column(name: "price", type: "float", nullable: true)]
    private ?float $price = null;

    #[ORM\Column(name: "advance", type: "float", nullable: true)]
    private ?float $advance = null;

    #[ORM\Column(name: "royalty", type: "integer", nullable: true)]
    private ?int $royalty = null;

    #[ORM\Column(name: "ytd_sales", type: "integer", nullable: true)]
    private ?int $ytdSales = null;

    #[ORM\Column(name: "notes", type: "string", length: 200, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: "pubdate", type: "datetime")]
    private \DateTimeInterface $pubdate;

    public function __construct()
    {
        $this->pubdate = new \DateTime();
    }

    # region Getters / Setters
    public function getId(): string { return $this->id; }
    public function setId(string $id): self { $this->id = $id; return $this; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }

    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }

    public function getPubId(): ?string { return $this->pubId; }
    public function setPubId(?string $pubId): self { $this->pubId = $pubId; return $this; }

    public function getPrice(): ?float { return $this->price; }
    public function setPrice(?float $price): self { $this->price = $price; return $this; }

    public function getAdvance(): ?float { return $this->advance; }
    public function setAdvance(?float $advance): self { $this->advance = $advance; return $this; }

    public function getRoyalty(): ?int { return $this->royalty; }
    public function setRoyalty(?int $royalty): self { $this->royalty = $royalty; return $this; }

    public function getYtdSales(): ?int { return $this->ytdSales; }
    public function setYtdSales(?int $ytdSales): self { $this->ytdSales = $ytdSales; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }

    public function getPubdate(): \DateTimeInterface { return $this->pubdate; }
    public function setPubdate(\DateTimeInterface $pubdate): self { $this->pubdate = $pubdate; return $this; }
    # endregion
}
