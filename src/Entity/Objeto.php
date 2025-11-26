<?php

namespace App\Entity;

use App\Repository\ObjetoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ObjetoRepository::class)
 * @UniqueEntity(fields={"codigo","subrubro"}, message="Ya Existe el objeto")
 */
class Objeto
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
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity=SubRubro::class, inversedBy="objetos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subrubro;

    /**
     * @ORM\OneToMany(targetEntity=ObjetoInventario::class, mappedBy="objeto")
     */
    private $objetoInventarios;

    public function __construct()
    {
        $this->objetoInventarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

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

    public function getSubrubro(): ?SubRubro
    {
        return $this->subrubro;
    }

    public function setSubrubro(?SubRubro $subrubro): self
    {
        $this->subrubro = $subrubro;

        return $this;
    }

    public function getCodigoCompleto(): ?string
    {
        return $this->subrubro->getRubro()->getCategoria() . " - " 
               . $this->subrubro->getCategoria() . " - " 
               . $this->getCodigo();
    }

    public function getCodigoYNombre(): ?string
    {
        return $this->subrubro->getRubro()->getCategoria() . " - " 
               . $this->subrubro->getCategoria() . " - " 
               . $this->getCodigo() . " - " . $this->getNombre();
    }    

    public function __toString(): ?string
    {
        return $this->subrubro . " - " . $this->getCodigo() . " " .  $this->nombre;
    }

    /**
     * @return Collection<int, ObjetoInventario>
     */
    public function getObjetoInventarios(): Collection
    {
        return $this->objetoInventarios;
    }

    public function addObjetoInventario(ObjetoInventario $objetoInventario): self
    {
        if (!$this->objetoInventarios->contains($objetoInventario)) {
            $this->objetoInventarios[] = $objetoInventario;
            $objetoInventario->setObjeto($this);
        }

        return $this;
    }

    public function removeObjetoInventario(ObjetoInventario $objetoInventario): self
    {
        if ($this->objetoInventarios->removeElement($objetoInventario)) {
            // set the owning side to null (unless already changed)
            if ($objetoInventario->getObjeto() === $this) {
                $objetoInventario->setObjeto(null);
            }
        }

        return $this;
    }      
}
