<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'anime_topic')]
class AnimeTopic extends Topic
{
    #[ORM\Column(length: 255)]
    private ?string $animeTitle = null;

    #[ORM\Column(length: 50)]
    private ?string $episode = null;

    public function getAnimeTitle(): ?string
    {
        return $this->animeTitle;
    }

    public function setAnimeTitle(string $animeTitle): static
    {
        $this->animeTitle = $animeTitle;
        return $this;
    }

    public function getEpisode(): ?string
    {
        return $this->episode;
    }

    public function setEpisode(string $episode): static
    {
        $this->episode = $episode;
        return $this;
    }

    public function getType(): string
    {
        return 'anime';
    }
} 