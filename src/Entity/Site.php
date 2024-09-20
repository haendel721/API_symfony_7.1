<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    // #[ORM\Column(length: 255)]
    // private ?string $category = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorySite $categorySite = null;

    #[ORM\OneToOne(inversedBy: 'site', cascade: ['persist', 'remove'])]
    private ?LoginSite $login = null;
    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    // public function getCategory(): ?string
    // {
    //     return $this->category;
    // }

    // public function setCategory(string $category): static
    // {
    //     $this->category = $category;

    //     return $this;
    // }

    public function getCategorySite(): ?CategorySite
    {
        return $this->categorySite;
    }

    public function setCategorySite(?CategorySite $categorySite): static
    {
        $this->categorySite = $categorySite;

        return $this;
    }

    // public function getLogin(): ?string
    // {
    //     return $this->login;
    // }

    // public function setLogin(string $login): static
    // {
    //     $this->login = $login;

    //     return $this;
    // }

    // public function getPassword(): ?string
    // {
    //     return $this->password;
    // }

    // public function setPassword(string $password): static
    // {
    //     $this->password = $password;

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, Password>
    //  */
    // public function getPasswords(): Collection
    // {
    //     return $this->passwords;
    // }

    // public function addPassword(Password $password): static
    // {
    //     if (!$this->passwords->contains($password)) {
    //         $this->passwords->add($password);
    //         $password->setSite($this);
    //     }

    //     return $this;
    // }

    // public function removePassword(Password $password): static
    // {
    //     if ($this->passwords->removeElement($password)) {
    //         // set the owning side to null (unless already changed)
    //         if ($password->getSite() === $this) {
    //             $password->setSite(null);
    //         }
    //     }

    //     return $this;
    // }

    public function getLogin(): ?LoginSite
    {
        return $this->login;
    }

    public function setLogin(?LoginSite $login): static
    {
        $this->login = $login;

        return $this;
    }
}
