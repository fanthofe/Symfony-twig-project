<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $ressource = null;

    #[ORM\Column]
    private ?int $estimation_duration = null;

    #[ORM\Column(length: 255)]
    private ?string $status = "ACTIVE";

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy:'projects')]
    private User $createdBy;

    #[ORM\ManyToOne(inversedBy: 'project')]
    private ?enterprise $enterprise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRessource(): ?int
    {
        return $this->ressource;
    }

    public function setRessource(int $ressource): static
    {
        $this->ressource = $ressource;

        return $this;
    }

    public function getEstimationDuration(): ?int
    {
        return $this->estimation_duration;
    }

    public function setEstimationDuration(int $estimation_duration): static
    {
        $this->estimation_duration = $estimation_duration;

        return $this;
    }

    public function getEnterpriseId(): ?enterprise
    {
        return $this->enterprise;
    }

    public function setEnterpriseId(?enterprise $enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));

        if($this->getCreatedAt() == null){
            $this->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
        }
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
