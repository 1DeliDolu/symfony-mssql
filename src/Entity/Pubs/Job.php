<?php
namespace App\Entity\Pubs;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "jobs")]
class Job
{
    #[ORM\Id]
    #[ORM\Column(name: "job_id", type: "smallint")]
    #[ORM\GeneratedValue(strategy: "IDENTITY")] // MSSQL IDENTITY(1,1)
    private ?int $id = null;

    #[ORM\Column(name: "job_desc", type: "string", length: 50, options: ["default" => "New Position - title not formalized yet"])]
    private string $description = 'New Position - title not formalized yet';

    // MSSQL tinyint -> Doctrine smallint
    #[ORM\Column(name: "min_lvl", type: "smallint")]
    private int $minLvl;

    #[ORM\Column(name: "max_lvl", type: "smallint")]
    private int $maxLvl;

    // Getters / Setters
    public function getId(): ?int { return $this->id; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $v): self { $this->description = $v; return $this; }

    public function getMinLvl(): int { return $this->minLvl; }
    public function setMinLvl(int $v): self { $this->minLvl = $v; return $this; }

    public function getMaxLvl(): int { return $this->maxLvl; }
    public function setMaxLvl(int $v): self { $this->maxLvl = $v; return $this; }
}
