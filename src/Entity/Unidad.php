<?php

namespace App\Entity;

use App\Repository\UnidadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UnidadRepository::class)
 */
class Unidad
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="unidad")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Inventario::class, mappedBy="unidad")
     */
    private $inventarios;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->inventarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setUnidad($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUnidad() === $this) {
                $user->setUnidad(null);
            }
        }

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->nombre;
    }

    /**
     * @return Collection<int, Inventario>
     */
    public function getInventarios(): Collection
    {
        return $this->inventarios;
    }

    public function addInventario(Inventario $inventario): self
    {
        if (!$this->inventarios->contains($inventario)) {
            $this->inventarios[] = $inventario;
            $inventario->setUnidad($this);
        }

        return $this;
    }

    public function removeInventario(Inventario $inventario): self
    {
        if ($this->inventarios->removeElement($inventario)) {
            // set the owning side to null (unless already changed)
            if ($inventario->getUnidad() === $this) {
                $inventario->setUnidad(null);
            }
        }

        return $this;
    }       
}
