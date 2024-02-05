<?php

namespace App\Entity;

use App\Repository\ChatImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatImageRepository::class)]
class ChatImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'image', targetEntity: ChatMessage::class)]
    private Collection $imageChatMessage;

    public function __construct()
    {
        $this->imageChatMessage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));

        if($this->getCreatedAt() == null){
            $this->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        }
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getImageChatMessage(): Collection
    {
        return $this->imageChatMessage;
    }

    public function addImageChatMessage(ChatMessage $imageChatMessage): static
    {
        if (!$this->imageChatMessage->contains($imageChatMessage)) {
            $this->imageChatMessage->add($imageChatMessage);
            $imageChatMessage->setImage($this);
        }

        return $this;
    }

    public function removeImageChatMessage(ChatMessage $imageChatMessage): static
    {
        if ($this->imageChatMessage->removeElement($imageChatMessage)) {
            // set the owning side to null (unless already changed)
            if ($imageChatMessage->getImage() === $this) {
                $imageChatMessage->setImage(null);
            }
        }

        return $this;
    }
}
