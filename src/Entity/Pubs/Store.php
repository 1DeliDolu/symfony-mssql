<?php
namespace App\Entity\Pubs;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "stores")]
class Store
{
    #[ORM\Id]
    #[ORM\Column(name: "stor_id", type: "string", length: 4)]
    private string $id;

    #[ORM\Column(name: "stor_name", type: "string", length: 40, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: "stor_address", type: "string", length: 40, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(name: "city", type: "string", length: 20, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name: "state", type: "string", length: 2, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(name: "zip", type: "string", length: 5, nullable: true)]
    private ?string $zip = null;

    // getters/setters
    public function getId(): string { return $this->id; }
    public function setId(string $id): self { $this->id = $id; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $v): self { $this->name = $v; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $v): self { $this->address = $v; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $v): self { $this->city = $v; return $this; }

    public function getState(): ?string { return $this->state; }
    public function setState(?string $v): self { $this->state = $v; return $this; }

    public function getZip(): ?string { return $this->zip; }
    public function setZip(?string $v): self { $this->zip = $v; return $this; }
}
