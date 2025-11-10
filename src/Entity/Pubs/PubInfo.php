<?php
namespace App\Entity\Pubs;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "pub_info")]
class PubInfo
{
    #[ORM\Id]
    #[ORM\Column(name: "pub_id", type: "string", length: 4)]
    private string $pubId;

    #[ORM\OneToOne(targetEntity: Publisher::class)]
    #[ORM\JoinColumn(name: "pub_id", referencedColumnName: "pub_id", nullable: false, onDelete: "CASCADE")]
    private ?Publisher $publisher = null;

    // SQL Server 'image' -> binary / blob olarak tutulabilir
    #[ORM\Column(name: "logo", type: "blob", nullable: true)]
    private $logo = null;

    // 'text' -> long text
    #[ORM\Column(name: "pr_info", type: "text", nullable: true)]
    private ?string $prInfo = null;

    public function getPubId(): string { return $this->pubId; }
    public function setPubId(string $id): self { $this->pubId = $id; return $this; }

    public function getPublisher(): ?Publisher { return $this->publisher; }
    public function setPublisher(?Publisher $publisher): self { $this->publisher = $publisher; return $this; }

    public function getLogo() { return $this->logo; }
    public function setLogo($logo): self { $this->logo = $logo; return $this; }

    public function getPrInfo(): ?string { return $this->prInfo; }
    public function setPrInfo(?string $v): self { $this->prInfo = $v; return $this; }
}
