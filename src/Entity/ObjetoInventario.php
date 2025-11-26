<?php

namespace App\Entity;

use App\Repository\ObjetoInventarioRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ObjetoInventarioRepository::class)
 */
class ObjetoInventario
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Objeto::class, inversedBy="objetoInventarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $objeto;

    /**
     * @ORM\Column(type="integer")
     */
    private $alta;

    /**
     * @ORM\Column(type="integer")
     */
    private $baja;

    /**
     * @ORM\Column(type="integer")
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity=Inventario::class, inversedBy="objetoInventarios")
     * @ORM\JoinColumn(nullable=false)
     */
    private $inventario;

    /**
     * @ORM\Column(type="integer")
     */
    private $alta_baja;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motivo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $tipo_bien;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjeto(): ?Objeto
    {
        return $this->objeto;
    }

    public function setObjeto(?Objeto $objeto): self
    {
        $this->objeto = $objeto;

        return $this;
    }

    public function getAlta(): ?int
    {
        return $this->alta;
    }

    public function setAlta(int $alta): self
    {
        $this->alta = $alta;

        return $this;
    }

    public function getBaja(): ?int
    {
        return $this->baja;
    }

    public function setBaja(int $baja): self
    {
        $this->baja = $baja;

        return $this;
    }

    public function getEstado(): ?int
    {
        return $this->estado;
    }

    public function setEstado(int $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getEstadoActual(): ?string
    {
        $estado_enum = ['0'=>'MALO', '1' => 'REGULAR', '2' => 'BUENO', '3' => 'MUY BUENO', '4' => 'NUEVO'];
        
        return $estado_enum[$this->estado];
    }    

    /*
    public function getCodigoCompleto(): ?string
    {
        return $this->objeto->subrubro->getRubro()->getCategoria() . " - " . $this->objeto->subrubro->getCategoria() . " - " . $this->objeto->getCodigo();
    } 
    */   

    public function getInventario(): ?inventario
    {
        return $this->inventario;
    }

    public function setInventario(?inventario $inventario): self
    {
        $this->inventario = $inventario;

        return $this;
    }

    public function getAltaBaja(): ?int
    {
        return $this->alta_baja;
    }

    public function setAltaBaja(int $alta_baja): self
    {
        $this->alta_baja = $alta_baja;

        return $this;
    }

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): self
    {
        $this->motivo = $motivo;

        return $this;
    }

    public function getExistencias(){
        return "";
    }

    public function __toString(): ?string
    {
        $tmp = "";
        if ($this->getAltaBaja() == 0 ){
            $tmp = "ALTA (" . $this->alta . ") "; 
        } else if ($this->getAltaBaja() == 1) {
            $tmp = "BAJA (" . $this->baja . ") ";
        } else {
            $tmp = "";
        }
        return $tmp . $this->motivo;
    }

    public function getEsTipoBien(): ?string
    {
        return $this->tipo_bien ? "PR" : "CA";
    }
    
    public function getTipoBien(): ?bool
    {
        return $this->tipo_bien;
    }

    public function setTipoBien(bool $tipo_bien): self
    {
        $this->tipo_bien = $tipo_bien;

        return $this;
    }

    
}
