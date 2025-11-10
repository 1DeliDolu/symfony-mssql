<?php
namespace App\Entity\Pubs;

use App\Repository\PublisherRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublisherRepository::class)]
#[ORM\Table(name: "publishers")]
class Publisher
{
    #[ORM\Id]
    #[ORM\Column(name: "pub_id", type: "string", length: 4)]
    private string $id;

    #[ORM\Column(name: "pub_name", type: "string", length: 40, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(name: "city", type: "string", length: 20, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name: "state", type: "string", length: 2, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(name: "country", type: "string", length: 30, nullable: true, options: ["default" => "USA"])]
    private ?string $country = 'USA';

    # region Getters/Setters
    public function getId(): string { return $this->id; }
    public function setId(string $id): self { $this->id = $id; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): self { $this->name = $name; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): self { $this->city = $city; return $this; }

    public function getState(): ?string { return $this->state; }
    public function setState(?string $state): self { $this->state = $state; return $this; }

    public function getCountry(): ?string { return $this->country; }
    public function setCountry(?string $country): self { $this->country = $country; return $this; }
    # endregion
}
