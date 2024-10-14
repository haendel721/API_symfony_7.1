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

    #[ORM\OneToMany(targetEntity: Permission::class, mappedBy: 'site')]
    
    private $permissions;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorySite $categorySite = null;

    #[ORM\OneToOne(inversedBy: 'site', cascade: ['persist', 'remove'])]
    private ?LoginSite $login = null;

    #[ORM\Column(type: 'string')]
    private ?string $id_login = null;

    #[ORM\Column(type: 'string')]
    private ?string $class_login = null;

    #[ORM\Column(type: 'string')]
    private ?string $id_mdp = null;

    #[ORM\Column(type: 'string')]
    private ?string $class_mdp = null;

    #[ORM\Column(type: 'string')]
    private ?string $class_submit = null;
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

    public function getIdLogin(): ?string
    {
        return $this->id_login;
    }

    public function setIdLogin(string $id_login): static
    {
        $this->id_login = $id_login;

        return $this;
    }

    public function getClassLogin(): ?string
    {
        return $this->class_login;
    }

    public function setClassLogin(string $class_login): static
    {
        $this->class_login = $class_login;

        return $this;
    }

    public function getIdMdp(): ?string
    {
        return $this->id_mdp;
    }

    public function setIdMdp(string $id_mdp): static
    {
        $this->id_mdp = $id_mdp;

        return $this;
    }

    public function getClassMdp(): ?string
    {
        return $this->class_mdp;
    }

    public function setClassMdp(string $class_mdp): static
    {
        $this->class_mdp = $class_mdp;

        return $this;
    }

    public function getClassSubmit(): ?string
    {
        return $this->class_submit;
    }

    public function setClassSubmit(string $class_submit): static
    {
        $this->class_submit = $class_submit;

        return $this;
    }
     /**
     * @return Collection|Permission[]
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions[] = $permission;
            $permission->setSite($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        if ($this->permissions->removeElement($permission)) {
            // set the owning side to null (unless already changed)
            if ($permission->getUser() === $this) {
                $permission->setUser(null);
            }
        }

        return $this;
    }

    public function hasPermissionToVisit(Site $site): bool
    {
        foreach ($this->permissions as $permission) {
            if ($permission->getSite() === $site && $permission->isAuthorized()) {
                return true;
            }
        }

        return false;
    }
}
