<?php

namespace App\Entity;

use App\Repository\ChatMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatMessageRepository::class)]
class ChatMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'chatMessages')]
    private ?Chat $chatId = null;

    #[ORM\Column]
    private ?bool $hasDropdown = null;

    #[ORM\ManyToOne(inversedBy: 'userSenderChatMessage')]
    private ?User $userSenderId = null;

    #[ORM\ManyToOne(inversedBy: 'userSenderChatMessage')]
    private ?User $userReceiverId = null;

    #[ORM\ManyToOne(inversedBy: 'isRepliedChatMessage')]
    private ?User $isReplied = null;

    #[ORM\ManyToOne(inversedBy: 'imageChatMessage')]
    private ?ChatImage $image = null;

    #[ORM\ManyToOne(inversedBy: 'fileChatMessage')]
    private ?ChatFiles $file = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    public function getChatId(): ?Chat
    {
        return $this->chatId;
    }

    public function setChatId(?Chat $chatId): static
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function isHasDropdown(): ?bool
    {
        return $this->hasDropdown;
    }

    public function setHasDropdown(bool $hasDropdown): static
    {
        $this->hasDropdown = $hasDropdown;

        return $this;
    }

    public function getUserSenderId(): ?User
    {
        return $this->userSenderId;
    }

    public function setUserSenderId(?User $userSenderId): static
    {
        $this->userSenderId = $userSenderId;

        return $this;
    }

    public function getUserReceiverId(): ?User
    {
        return $this->userReceiverId;
    }

    public function setUserReceiverId(?User $userReceiverId): static
    {
        $this->userReceiverId = $userReceiverId;

        return $this;
    }

    public function getIsReplied(): ?User
    {
        return $this->isReplied;
    }

    public function setIsReplied(?User $isReplied): static
    {
        $this->isReplied = $isReplied;

        return $this;
    }

    public function getImage(): ?ChatImage
    {
        return $this->image;
    }

    public function setImage(?ChatImage $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getFile(): ?ChatFiles
    {
        return $this->file;
    }

    public function setFile(?ChatFiles $file): static
    {
        $this->file = $file;

        return $this;
    }

}
