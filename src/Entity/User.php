<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
// use Symfony\Component\Validator\Constraints\Collection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $lot = null;

    #[ORM\Column]
    private ?int $tel = null;

    #[ORM\Column( length:255, nullable:true)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $situationFamiliale = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $genre = null;

    #[ORM\Column(length: 255)]
    private ?string $fonction = null;

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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getLot(): ?string
    {
        return $this->lot;
    }

    public function setLot(string $lot): static
    {
        $this->lot = $lot;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getSituationFamiliale(): ?string
    {
        return $this->situationFamiliale;
    }

    public function setSituationFamiliale(string $situationFamiliale): static
    {
        $this->situationFamiliale = $situationFamiliale;

        return $this;
    }

    public function getLieuNaissance(): ?string
    {
        return $this->lieuNaissance;
    }

    public function setLieuNaissance(string $lieuNaissance): static
    {
        $this->lieuNaissance = $lieuNaissance;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }
    #[ORM\OneToMany(targetEntity: Permission::class, mappedBy: 'user')]
    
    private $permissions;

    /**
     * @var Collection<int, LoginSite>
     */
    #[ORM\OneToMany(targetEntity: LoginSite::class, mappedBy: 'user')]
    private Collection $loginSites;

    public function __construct()
    {
        $this->loginSites = new ArrayCollection();
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
            $permission->setUser($this);
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

    /**
     * @return Collection<int, LoginSite>
     */
    public function getLoginSites(): Collection
    {
        return $this->loginSites;
    }

    public function addLoginSite(LoginSite $loginSite): static
    {
        if (!$this->loginSites->contains($loginSite)) {
            $this->loginSites->add($loginSite);
            $loginSite->setUser($this);
        }

        return $this;
    }

    public function removeLoginSite(LoginSite $loginSite): static
    {
        if ($this->loginSites->removeElement($loginSite)) {
            // set the owning side to null (unless already changed)
            if ($loginSite->getUser() === $this) {
                $loginSite->setUser(null);
            }
        }

        return $this;
    }

}
