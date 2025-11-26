<?php

namespace App\Entity;

use App\Repository\InventarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InventarioRepository::class)
 */
class Inventario
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\OneToMany(targetEntity=ObjetoInventario::class, mappedBy="inventario")
     */
    private $objetoInventarios;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity=Unidad::class, inversedBy="inventarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $unidad;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estado;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha_cierre;

    public function __construct()
    {
        $this->objetoInventarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
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
            $objetoInventario->setInventario($this);
        }

        return $this;
    }

    public function removeObjetoInventario(ObjetoInventario $objetoInventario): self
    {
        if ($this->objetoInventarios->removeElement($objetoInventario)) {
            // set the owning side to null (unless already changed)
            if ($objetoInventario->getInventario() === $this) {
                $objetoInventario->setInventario(null);
            }
        }

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->nombre;
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

    public function getUnidad(): ?Unidad
    {
        return $this->unidad;
    }

    public function setUnidad(?Unidad $unidad): self
    {
        $this->unidad = $unidad;

        return $this;
    }

    public function getEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFechaCierre(): ?\DateTimeInterface
    {
        return $this->fecha_cierre;
    }

    public function setFechaCierre(?\DateTimeInterface $fecha_cierre): self
    {
        $this->fecha_cierre = $fecha_cierre;

        return $this;
    }      
}
