<?php

namespace App\Entity;

use App\Repository\UserDateEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDateEventRepository::class)]
class UserDateEvent
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

    #[ORM\ManyToOne(inversedBy: 'userDateEvents', targetEntity: User::class)]
    protected ?user $user = null;

    #[ORM\ManyToOne(inversedBy: 'dateEventsUser', targetEntity: DateEvent::class)]
    protected ?DateEvent $dateEvent = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDateEvent(): ?DateEvent
    {
        return $this->dateEvent;
    }

    public function setDateEvent(?DateEvent $dateEvent): self
    {
        $this->dateEvent = $dateEvent;

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
}
