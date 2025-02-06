<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "user" => User::class,
    "organizer" => Organizer::class
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    protected ?string $email = null;

    #[ORM\Column(length: 255)]
    protected ?string $firstName = null;

    #[ORM\Column(length: 255)]
    protected ?string $lastName = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'participants')]
    private Collection $participatingEvents;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'organizers')]
    private Collection $organizedEvents;

    /**
     * @var Collection<int, Community>
     */
    #[ORM\OneToMany(targetEntity: Community::class, mappedBy: 'creator')]
    private Collection $communities;

    /**
     * @var Collection<int, Topic>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Topic::class, orphanRemoval: true)]
    private Collection $topics;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(nullable: true)]
    protected ?bool $isSubscribedToNewsletter = false;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $preferences = null;

    public function __construct()
    {
        $this->roles = ['ROLE_USER', 'ROLE_PARTICIPANT'];
        $this->communities = new ArrayCollection();
        $this->topics = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->participatingEvents = new ArrayCollection();
        $this->organizedEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Always add ROLE_USER and ROLE_PARTICIPANT
        $roles[] = 'ROLE_USER';
        $roles[] = 'ROLE_PARTICIPANT';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        // Ensure ROLE_PARTICIPANT is preserved when setting new roles
        $roles[] = 'ROLE_PARTICIPANT';
        $this->roles = array_unique($roles);
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): static
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return Collection<int, Community>
     */
    public function getCommunities(): Collection
    {
        return $this->communities;
    }

    public function addCommunity(Community $community): static
    {
        if (!$this->communities->contains($community)) {
            $this->communities->add($community);
            $community->setCreator($this);
        }

        return $this;
    }

    public function removeCommunity(Community $community): static
    {
        if ($this->communities->removeElement($community)) {
            // set the owning side to null (unless already changed)
            if ($community->getCreator() === $this) {
                $community->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Topic>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topic $topic): static
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
            $topic->setAuthor($this);
        }

        return $this;
    }

    public function removeTopic(Topic $topic): static
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getAuthor() === $this) {
                $topic->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): static
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function isAdult(): bool
    {
        if (!$this->birthDate) {
            return false;
        }

        $age = $this->birthDate->diff(new \DateTime())->y;
        return $age >= 18;
    }

    public function getParticipantingEvents(): Collection
    {
        return $this->participatingEvents;
    }

    //setter paticipating events
    public function setParticipatingEvents(Collection $participatingEvents): static
    {
        $this->participatingEvents = $participatingEvents;
        return $this;
    }

    //organized events
    public function getOrganizedEvents(): Collection
    {
        return $this->organizedEvents;
    }

    //setter organized events
    public function setOrganizedEvents(Collection $organizedEvents): static
    {
        $this->organizedEvents = $organizedEvents;
        return $this;
    }

    public function getIsSubscribedToNewsletter(): ?bool
    {
        return $this->isSubscribedToNewsletter;
    }

    public function setIsSubscribedToNewsletter(?bool $isSubscribedToNewsletter): self
    {
        $this->isSubscribedToNewsletter = $isSubscribedToNewsletter;
        return $this;
    }

    public function getPreferences(): ?string
    {
        return $this->preferences;
    }

    public function setPreferences(?string $preferences): self
    {
        $this->preferences = $preferences;
        return $this;
    }
}
