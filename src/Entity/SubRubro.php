<?php

namespace App\Entity;

use App\Repository\SubRubroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=SubRubroRepository::class)
 * @UniqueEntity(fields={"categoria","rubro"}, message="Ya Existe la categoria y el rubro indicado")
 */
class SubRubro
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
     * @ORM\ManyToOne(targetEntity=Rubro::class, inversedBy="subRubros")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rubro;

    /**
     * @ORM\OneToMany(targetEntity=Objeto::class, mappedBy="subrubro")
     */
    private $objetos;

    public function __construct()
    {
        $this->objetos = new ArrayCollection();
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

    public function getRubro(): ?Rubro
    {
        return $this->rubro;
    }

    public function setRubro(?Rubro $rubro): self
    {
        $this->rubro = $rubro;

        return $this;
    }

    /**
     * @return Collection<int, Objeto>
     */
    public function getObjetos(): Collection
    {
        return $this->objetos;
    }

    public function addObjeto(Objeto $objeto): self
    {
        if (!$this->objetos->contains($objeto)) {
            $this->objetos[] = $objeto;
            $objeto->setSubrubro($this);
        }

        return $this;
    }

    public function removeObjeto(Objeto $objeto): self
    {
        if ($this->objetos->removeElement($objeto)) {
            // set the owning side to null (unless already changed)
            if ($objeto->getSubrubro() === $this) {
                $objeto->setSubrubro(null);
            }
        }

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->rubro->getCategoria() . " " . $this->rubro->getNombre() . " - " . $this->categoria . " " . $this->nombre;
    }    
}
