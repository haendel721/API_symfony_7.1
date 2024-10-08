<?php

namespace App\Entity;

use App\Repository\LoginSiteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginSiteRepository::class)]
class LoginSite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
     

    #[ORM\Column(length: 255)]
    private ?string $nameSite = null;

    #[ORM\Column(length: 255)]
    private ?string $login = null;

    #[ORM\Column(length: 255)]
    private ?string $mdp = null;

    // #[ORM\ManyToOne(inversedBy: 'loginSites')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'loginSites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'login', cascade: ['persist', 'remove'])]
    private ?Site $site = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNameSite(): ?string
    {
        return $this->nameSite;
    }

    public function setNameSite(string $nameSite): static
    {
        $this->nameSite = $nameSite;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;

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
        // unset the owning side of the relation if necessary
        if ($site === null && $this->site !== null) {
            $this->site->setLogin(null);
        }

        // set the owning side of the relation if necessary
        if ($site !== null && $site->getLogin() !== $this) {
            $site->setLogin($this);
        }

        $this->site = $site;

        return $this;
    }

}
