<?php
namespace App\Entity\Pubs;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "employee")]
class Employee
{
    #[ORM\Id]
    #[ORM\Column(name: "emp_id", type: "string", length: 9)]
    private string $empId;

    #[ORM\Column(name: "fname", type: "string", length: 20)]
    private string $firstName;

    #[ORM\Column(name: "minit", type: "string", length: 1, nullable: true)]
    private ?string $middleInitial = null;

    #[ORM\Column(name: "lname", type: "string", length: 30)]
    private string $lastName;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(name: "job_id", referencedColumnName: "job_id", nullable: false)]
    private Job $job;

    #[ORM\Column(name: "job_lvl", type: "smallint", options: ["default" => 10])]
    private int $jobLevel = 10;

    #[ORM\ManyToOne(targetEntity: Publisher::class)]
    #[ORM\JoinColumn(name: "pub_id", referencedColumnName: "pub_id", nullable: false)]
    private Publisher $publisher;

    #[ORM\Column(name: "hire_date", type: "datetime")]
    private \DateTimeInterface $hireDate;

    public function getEmpId(): string { return $this->empId; }
    public function setEmpId(string $id): self { $this->empId = $id; return $this; }

    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $f): self { $this->firstName = $f; return $this; }

    public function getMiddleInitial(): ?string { return $this->middleInitial; }
    public function setMiddleInitial(?string $m): self { $this->middleInitial = $m; return $this; }

    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $l): self { $this->lastName = $l; return $this; }

    public function getJob(): Job { return $this->job; }
    public function setJob(Job $j): self { $this->job = $j; return $this; }

    public function getJobLevel(): int { return $this->jobLevel; }
    public function setJobLevel(int $lvl): self { $this->jobLevel = $lvl; return $this; }

    public function getPublisher(): Publisher { return $this->publisher; }
    public function setPublisher(Publisher $p): self { $this->publisher = $p; return $this; }

    public function getHireDate(): \DateTimeInterface { return $this->hireDate; }
    public function setHireDate(\DateTimeInterface $d): self { $this->hireDate = $d; return $this; }
}
