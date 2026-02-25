<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'produit')]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du produit est obligatoire.")]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\PositiveOrZero(message: "Le prix doit être positif.")]
    private ?string $prix = null; 

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: "Le stock actuel doit être positif.")]
    private ?int $stockActuel = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: "Le stock minimum doit être positif.")]
    private ?int $stockMin = 0;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Veuillez choisir un fournisseur.")]
    private ?Fournisseur $fournisseur = null;

    // ✅ inverse side des mouvements (Mouvementstock)
    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Mouvementstock::class, orphanRemoval: true)]
    private Collection $mouvementsStocks;

    public function __construct()
    {
        $this->mouvementsStocks = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getPrix(): ?string { return $this->prix; }
    public function setPrix(string $prix): self { $this->prix = $prix; return $this; }

    public function getStockActuel(): int { return (int)$this->stockActuel; }
    public function setStockActuel(int $stockActuel): self { $this->stockActuel = $stockActuel; return $this; }

    public function getStockMin(): int { return (int)$this->stockMin; }
    public function setStockMin(int $stockMin): self { $this->stockMin = $stockMin; return $this; }

    public function getFournisseur(): ?Fournisseur { return $this->fournisseur; }
    public function setFournisseur(?Fournisseur $fournisseur): self { $this->fournisseur = $fournisseur; return $this; }

    // ✅ mouvementsStocks
    public function getMouvementsStocks(): Collection
    {
        return $this->mouvementsStocks;
    }

    public function addMouvementStock(Mouvementstock $mouvementStock): self
    {
        if (!$this->mouvementsStocks->contains($mouvementStock)) {
            $this->mouvementsStocks->add($mouvementStock);
            $mouvementStock->setProduit($this);
        }
        return $this;
    }

    public function removeMouvementStock(Mouvementstock $mouvementStock): self
    {
        if ($this->mouvementsStocks->removeElement($mouvementStock)) {
            if ($mouvementStock->getProduit() === $this) {
                $mouvementStock->setProduit(null);
            }
        }
        return $this;
    }
}
