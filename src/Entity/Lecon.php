<?php

namespace App\Entity;

use App\Repository\LeconRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeconRepository::class)]
class Lecon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomLecon = null;

    #[ORM\ManyToOne(inversedBy: 'lecons')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'lecons')]
    private ?Programme $programme = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomLecon(): ?string
    {
        return $this->nomLecon;
    }

    public function setNomLecon(string $nomLecon): static
    {
        $this->nomLecon = $nomLecon;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getProgramme(): ?Programme
    {
        return $this->programme;
    }

    public function setProgramme(?Programme $programme): static
    {
        $this->programme = $programme;

        return $this;
    }
}
