<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $metaTitle = null;

    #[ORM\Column(length: 255)]
    private ?string $metaDescription = null;

    #[ORM\Column]
    private ?bool $isInMaintenance = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(string $metaTitle): static
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): static
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function isIsInMaintenance(): ?bool
    {
        return $this->isInMaintenance;
    }

    public function setIsInMaintenance(bool $isInMaintenance): static
    {
        $this->isInMaintenance = $isInMaintenance;

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
}
