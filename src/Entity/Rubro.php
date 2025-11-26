<?php

namespace App\Entity;

use App\Repository\RubroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=RubroRepository::class)
 * @UniqueEntity(fields={"categoria"}, message="Ya Existe la categoria")
 */
class Rubro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $categoria;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity=SubRubro::class, mappedBy="rubro")
     */
    private $subRubros;

    public function __construct()
    {
        $this->subRubros = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection<int, SubRubro>
     */
    public function getSubRubros(): Collection
    {
        return $this->subRubros;
    }

    public function addSubRubro(SubRubro $subRubro): self
    {
        if (!$this->subRubros->contains($subRubro)) {
            $this->subRubros[] = $subRubro;
            $subRubro->setRubro($this);
        }

        return $this;
    }

    public function removeSubRubro(SubRubro $subRubro): self
    {
        if ($this->subRubros->removeElement($subRubro)) {
            // set the owning side to null (unless already changed)
            if ($subRubro->getRubro() === $this) {
                $subRubro->setRubro(null);
            }
        }

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->categoria . " " . $this->nombre;
    }     
}
