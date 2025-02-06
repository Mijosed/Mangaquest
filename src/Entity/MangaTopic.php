<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'manga_topic')]
class MangaTopic extends Topic
{
    #[ORM\Column(length: 255)]
    private ?string $mangaTitle = null;

    #[ORM\Column(length: 50)]
    private ?string $chapter = null;

    public function getMangaTitle(): ?string
    {
        return $this->mangaTitle;
    }

    public function setMangaTitle(string $mangaTitle): static
    {
        $this->mangaTitle = $mangaTitle;
        return $this;
    }

    public function getChapter(): ?string
    {
        return $this->chapter;
    }

    public function setChapter(string $chapter): static
    {
        $this->chapter = $chapter;
        return $this;
    }

    public function getType(): string
    {
        return 'manga';
    }
} 