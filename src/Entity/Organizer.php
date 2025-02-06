<?php

namespace App\Entity;

use App\Repository\OrganizerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizerRepository::class)]
class Organizer extends User
{
    #[ORM\Column(length: 255)]
    private ?string $organization = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    public function __construct()
    {
        parent::__construct();
        $this->setRoles(['ROLE_ORGANIZER']);
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(string $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }
}
