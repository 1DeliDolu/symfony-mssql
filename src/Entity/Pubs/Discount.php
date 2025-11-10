<?php
namespace App\Entity\Pubs;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "discounts")]
class Discount
{
    #[ORM\Id]
    #[ORM\Column(name: "discounttype", type: "string", length: 40)]
    private string $discountType;

    #[ORM\ManyToOne(targetEntity: Store::class)]
    #[ORM\JoinColumn(name: "stor_id", referencedColumnName: "stor_id", nullable: true, onDelete: "SET NULL")]
    private ?Store $store = null;

    #[ORM\Column(name: "lowqty", type: "smallint", nullable: true)]
    private ?int $lowQty = null;

    #[ORM\Column(name: "highqty", type: "smallint", nullable: true)]
    private ?int $highQty = null;

    #[ORM\Column(name: "discount", type: "decimal", precision: 4, scale: 2)]
    private float $discount;

    // --- Getters/Setters ---
    public function getDiscountType(): string { return $this->discountType; }
    public function setDiscountType(string $type): self { $this->discountType = $type; return $this; }

    public function getStore(): ?Store { return $this->store; }
    public function setStore(?Store $store): self { $this->store = $store; return $this; }

    public function getLowQty(): ?int { return $this->lowQty; }
    public function setLowQty(?int $lowQty): self { $this->lowQty = $lowQty; return $this; }

    public function getHighQty(): ?int { return $this->highQty; }
    public function setHighQty(?int $highQty): self { $this->highQty = $highQty; return $this; }

    public function getDiscount(): float { return $this->discount; }
    public function setDiscount(float $discount): self { $this->discount = $discount; return $this; }
}
