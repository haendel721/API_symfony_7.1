<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;


    // #[ORM\ManyToOne]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?TypePermission $typePermission = null;

    #[ORM\Column]
    private ?bool $isAuthorized = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;


    public function getId(): ?int
    {
        return $this->id;
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


    // public function getTypePermission(): ?TypePermission
    // {
    //     return $this->typePermission;
    // }

    // public function setTypePermission(?TypePermission $typePermission): static
    // {
    //     $this->typePermission = $typePermission;

    //     return $this;
    // }

    public function isAuthorized(): ?bool
    {
        return $this->isAuthorized;
    }

    public function setAuthorized(bool $isAuthorized): static
    {
        $this->isAuthorized = $isAuthorized;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

}
