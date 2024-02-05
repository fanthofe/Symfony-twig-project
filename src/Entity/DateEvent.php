<?php

namespace App\Entity;

use App\Repository\DateEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DateEventRepository::class)]
class DateEvent
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

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $status = "ACTIVE";

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt;

    #[ORM\OneToMany(mappedBy: 'dateEvent', targetEntity: UserDateEvent::class)]
    protected Collection $dateEventsUser;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localisation = null;

    public function __construct()
    {
        $this->dateEventsUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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

    /**
     * @return Collection<int, UserDateEvent>
     */
    public function getUserDateEvents(): Collection
    {
        return $this->dateEventsUser;
    }

    public function addUserDateEvent(UserDateEvent $dateEventsUser): self
    {
        if (!$this->dateEventsUser->contains($dateEventsUser)) {
            $this->dateEventsUser->add($dateEventsUser);
            $dateEventsUser->setDateEvent($this);
        }

        return $this;
    }

    public function removeUserDateEvent(UserDateEvent $dateEventsUser): self
    {
        if ($this->dateEventsUser->removeElement($dateEventsUser)) {
            // set the owning side to null (unless already changed)
            if ($dateEventsUser->getDateEvent() === $this) {
                $dateEventsUser->setDateEvent(null);
            }
        }

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
}
