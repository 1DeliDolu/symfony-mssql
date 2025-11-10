<?php
namespace App\Entity\Pubs;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "roysched")]
class Roysched
{
    #[ORM\Id]
    #[ORM\Column(name: "title_id", type: "string", length: 6)]
    private string $titleId;

    #[ORM\ManyToOne(targetEntity: Title::class)]
    #[ORM\JoinColumn(name: "title_id", referencedColumnName: "title_id", nullable: false, onDelete: "CASCADE")]
    private ?Title $title = null;

    #[ORM\Column(name: "lorange", type: "integer", nullable: true)]
    private ?int $lorange = null;

    #[ORM\Column(name: "hirange", type: "integer", nullable: true)]
    private ?int $hirange = null;

    #[ORM\Column(name: "royalty", type: "integer", nullable: true)]
    private ?int $royalty = null;

    public function getTitleId(): string { return $this->titleId; }
    public function setTitleId(string $id): self { $this->titleId = $id; return $this; }

    public function getTitle(): ?Title { return $this->title; }
    public function setTitle(?Title $title): self { $this->title = $title; return $this; }

    public function getLorange(): ?int { return $this->lorange; }
    public function setLorange(?int $v): self { $this->lorange = $v; return $this; }

    public function getHirange(): ?int { return $this->hirange; }
    public function setHirange(?int $v): self { $this->hirange = $v; return $this; }

    public function getRoyalty(): ?int { return $this->royalty; }
    public function setRoyalty(?int $v): self { $this->royalty = $v; return $this; }
}
