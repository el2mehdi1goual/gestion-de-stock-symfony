<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'utilisateur')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string', length: 100)]
    private string $nom;

    #[ORM\Column(type: 'string', length: 100)]
    private string $prenom;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $telephone = null;

    // ✅ inverse side des mouvements (Mouvementstock)
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Mouvementstock::class, orphanRemoval: true)]
    private Collection $mouvementsStocks;

    public function __construct()
    {
        $this->mouvementsStocks = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return $this->email; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): self { $this->telephone = $telephone; return $this; }

    public function eraseCredentials(): void {}

    // ✅ mouvementsStocks
    public function getMouvementsStocks(): Collection
    {
        return $this->mouvementsStocks;
    }

    public function addMouvementStock(Mouvementstock $mouvementStock): self
    {
        if (!$this->mouvementsStocks->contains($mouvementStock)) {
            $this->mouvementsStocks->add($mouvementStock);
            $mouvementStock->setUser($this);
        }
        return $this;
    }

    public function removeMouvementStock(Mouvementstock $mouvementStock): self
    {
        if ($this->mouvementsStocks->removeElement($mouvementStock)) {
            if ($mouvementStock->getUser() === $this) {
                $mouvementStock->setUser(null);
            }
        }
        return $this;
    }
}
