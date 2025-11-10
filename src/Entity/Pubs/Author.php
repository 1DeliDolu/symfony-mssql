<?php
namespace App\Entity\Pubs;

use App\Repository\AuthorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
#[ORM\Table(name: "authors")]
class Author
{
    #[ORM\Id]
    #[ORM\Column(name: "au_id", type: "string", length: 11)]
    private string $id;

    #[ORM\Column(name: "au_lname", type: "string", length: 40)]
    private string $lastName;

    #[ORM\Column(name: "au_fname", type: "string", length: 20)]
    private string $firstName;

    #[ORM\Column(name: "phone", type: "string", length: 12, options: ["default" => "UNKNOWN"])]
    private string $phone = 'UNKNOWN';

    #[ORM\Column(name: "address", type: "string", length: 40, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(name: "city", type: "string", length: 20, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name: "state", type: "string", length: 2, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(name: "zip", type: "string", length: 5, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column(name: "contract", type: "boolean")]
    private bool $contract;

    # region Getters/Setters
    public function getId(): string { return $this->id; }
    public function setId(string $id): self { $this->id = $id; return $this; }

    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }

    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }

    public function getPhone(): string { return $this->phone; }
    public function setPhone(string $phone): self { $this->phone = $phone; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): self { $this->city = $city; return $this; }

    public function getState(): ?string { return $this->state; }
    public function setState(?string $state): self { $this->state = $state; return $this; }

    public function getZip(): ?string { return $this->zip; }
    public function setZip(?string $zip): self { $this->zip = $zip; return $this; }

    public function isContract(): bool { return $this->contract; }
    public function setContract(bool $contract): self { $this->contract = $contract; return $this; }
    # endregion
}
