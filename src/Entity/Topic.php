<?php

namespace App\Entity;

use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "topic_type", type: "string")]
#[ORM\DiscriminatorMap([
    "topic" => Topic::class,
    "manga" => MangaTopic::class,
    "anime" => AnimeTopic::class
])]
class Topic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'topics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    #[ORM\Column]
    private int $views = 0;

    #[ORM\Column]
    private bool $isApproved = false;

    #[ORM\Column]
    private bool $isNsfw = false;

    #[ORM\Column]
    private bool $hasSpoiler = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spoilerWarning = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFilename = null;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;
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

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): static
    {
        $this->views = $views;
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
            $post->setTopic($this);
        }
        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getTopic() === $this) {
                $post->setTopic(null);
            }
        }
        return $this;
    }

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;
        return $this;
    }

    public function isNsfw(): bool
    {
        return $this->isNsfw;
    }

    public function setIsNsfw(bool $isNsfw): static
    {
        $this->isNsfw = $isNsfw;
        return $this;
    }

    public function hasSpoiler(): bool
    {
        return $this->hasSpoiler;
    }

    public function setHasSpoiler(bool $hasSpoiler): static
    {
        $this->hasSpoiler = $hasSpoiler;
        return $this;
    }

    public function getSpoilerWarning(): ?string
    {
        return $this->spoilerWarning;
    }

    public function setSpoilerWarning(?string $spoilerWarning): static
    {
        $this->spoilerWarning = $spoilerWarning;
        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): static
    {
        $this->imageFilename = $imageFilename;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageFilename ? '/uploads/topics/' . $this->imageFilename : null;
    }

    public function getType(): string
    {
        return 'topic';
    }

    // Getters et Setters...
} 