<?php

namespace App\Entity;

use App\Repository\ChatFilesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatFilesRepository::class)]
class ChatFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $files = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'file', targetEntity: ChatMessage::class)]
    private Collection $fileChatMessage;

    public function __construct()
    {
        $this->fileChatMessage = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFiles(): ?string
    {
        return $this->files;
    }

    public function setFiles(string $files): static
    {
        $this->files = $files;

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
    public function getFileChatMessage(): Collection
    {
        return $this->fileChatMessage;
    }

    public function addFileChatMessage(ChatMessage $fileChatMessage): static
    {
        if (!$this->fileChatMessage->contains($fileChatMessage)) {
            $this->fileChatMessage->add($fileChatMessage);
            $fileChatMessage->setFile($this);
        }

        return $this;
    }

    public function removeFileChatMessage(ChatMessage $fileChatMessage): static
    {
        if ($this->fileChatMessage->removeElement($fileChatMessage)) {
            // set the owning side to null (unless already changed)
            if ($fileChatMessage->getFile() === $this) {
                $fileChatMessage->setFile(null);
            }
        }

        return $this;
    }
}
