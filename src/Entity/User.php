<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user_internal`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?string $firstName = null;

    #[ORM\Column]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $job = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_entry = null;

    #[ORM\Column(length: 255)]
    private ?string $status = "ACTIVE";

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'createdBy')]
    private Collection $clients;

    #[ORM\OneToMany(targetEntity: Enterprise::class, mappedBy: 'createdBy')]
    private Collection $enterprises;

    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'createdBy')]
    private Collection $projects;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilImage = "/images/users/user.png";

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserDateEvent::class)]
    protected Collection $userDateEvents;

    #[ORM\OneToMany(mappedBy: 'userSender', targetEntity: UserChat::class)]
    private Collection $userSender;

    #[ORM\OneToMany(mappedBy: 'userReceiver', targetEntity: UserChat::class)]
    private Collection $userReceiver;

    #[ORM\OneToMany(mappedBy: 'userSenderId', targetEntity: ChatMessage::class)]
    private Collection $userSenderChatMessage;

    #[ORM\OneToMany(mappedBy: 'isReplied', targetEntity: ChatMessage::class)]
    private Collection $isRepliedChatMessage;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->enterprises = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->userDateEvents = new ArrayCollection();
        $this->userSender = new ArrayCollection();
        $this->userReceiver = new ArrayCollection();
        $this->userSenderChatMessage = new ArrayCollection();
        $this->isRepliedChatMessage = new ArrayCollection();
    }

    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function getEnterprises(): Collection
    {
        return $this->enterprises;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDateEntry(): ?\DateTimeInterface
    {
        return $this->date_entry;
    }

    public function setDateEntry(?\DateTimeInterface $date_entry): static
    {
        $this->date_entry = $date_entry;

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

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getProfilImage(): ?string
    {
        return $this->profilImage;
    }

    public function setProfilImage(?string $profilImage): static
    {
        $this->profilImage = $profilImage;

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
        return $this->userDateEvents;
    }

    public function addUserDateEvent(UserDateEvent $userDateEvent): self
    {
        if (!$this->userDateEvents->contains($userDateEvent)) {
            $this->userDateEvents->add($userDateEvent);
            $userDateEvent->setUser($this);
        }

        return $this;
    }

    public function removeUserDateEvent(UserDateEvent $userDateEvent): self
    {
        if ($this->userDateEvents->removeElement($userDateEvent)) {
            // set the owning side to null (unless already changed)
            if ($userDateEvent->getUser() === $this) {
                $userDateEvent->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserChat>
     */
    public function getUserSender(): Collection
    {
        return $this->userSender;
    }

    public function addUserSender(UserChat $userSender): static
    {
        if (!$this->userSender->contains($userSender)) {
            $this->userSender->add($userSender);
            $userSender->setUserSender($this);
        }

        return $this;
    }

    public function removeUserSender(UserChat $userSender): static
    {
        if ($this->userSender->removeElement($userSender)) {
            // set the owning side to null (unless already changed)
            if ($userSender->getUserSender() === $this) {
                $userSender->setUserSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserChat>
     */
    public function getUserReceiver(): Collection
    {
        return $this->userReceiver;
    }

    public function addUserReceiver(UserChat $userReceiver): static
    {
        if (!$this->userReceiver->contains($userReceiver)) {
            $this->userReceiver->add($userReceiver);
            $userReceiver->setUserReceiver($this);
        }

        return $this;
    }

    public function removeUserReceiver(UserChat $userReceiver): static
    {
        if ($this->userReceiver->removeElement($userReceiver)) {
            // set the owning side to null (unless already changed)
            if ($userReceiver->getUserReceiver() === $this) {
                $userReceiver->setUserReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getUserSenderChatMessage(): Collection
    {
        return $this->userSenderChatMessage;
    }

    public function addUserSenderChatMessage(ChatMessage $userSenderChatMessage): static
    {
        if (!$this->userSenderChatMessage->contains($userSenderChatMessage)) {
            $this->userSenderChatMessage->add($userSenderChatMessage);
            $userSenderChatMessage->setUserSenderId($this);
        }

        return $this;
    }

    public function removeUserSenderChatMessage(ChatMessage $userSenderChatMessage): static
    {
        if ($this->userSenderChatMessage->removeElement($userSenderChatMessage)) {
            // set the owning side to null (unless already changed)
            if ($userSenderChatMessage->getUserSenderId() === $this) {
                $userSenderChatMessage->setUserSenderId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getIsRepliedChatMessage(): Collection
    {
        return $this->isRepliedChatMessage;
    }

    public function addIsRepliedChatMessage(ChatMessage $isRepliedChatMessage): static
    {
        if (!$this->isRepliedChatMessage->contains($isRepliedChatMessage)) {
            $this->isRepliedChatMessage->add($isRepliedChatMessage);
            $isRepliedChatMessage->setIsReplied($this);
        }

        return $this;
    }

    public function removeIsRepliedChatMessage(ChatMessage $isRepliedChatMessage): static
    {
        if ($this->isRepliedChatMessage->removeElement($isRepliedChatMessage)) {
            // set the owning side to null (unless already changed)
            if ($isRepliedChatMessage->getIsReplied() === $this) {
                $isRepliedChatMessage->setIsReplied(null);
            }
        }

        return $this;
    }
}
