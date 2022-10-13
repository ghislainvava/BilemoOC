<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "Product_detail",
 *          parameters = { "id" = "expr(object.getId())" }
 *      )
 * )
 *
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $modelname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ecran = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $capteurphoto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cpu = null;

    #[ORM\Column(nullable: true)]
    private ?int $ram = null;

    #[ORM\Column(nullable: true)]
    private ?int $stockage = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $remarques = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $marque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModelname(): ?string
    {
        return $this->modelname;
    }

    public function setModelname(?string $modelname): self
    {
        $this->modelname = $modelname;

        return $this;
    }

    public function getEcran(): ?string
    {
        return $this->ecran;
    }

    public function setEcran(?string $ecran): self
    {
        $this->ecran = $ecran;

        return $this;
    }

    public function getCapteurphoto(): ?string
    {
        return $this->capteurphoto;
    }

    public function setCapteurphoto(?string $capteurphoto): self
    {
        $this->capteurphoto = $capteurphoto;

        return $this;
    }

    public function getCpu(): ?string
    {
        return $this->cpu;
    }

    public function setCpu(?string $cpu): self
    {
        $this->cpu = $cpu;

        return $this;
    }

    public function getRam(): ?int
    {
        return $this->ram;
    }

    public function setRam(?int $ram): self
    {
        $this->ram = $ram;

        return $this;
    }

    public function getStockage(): ?int
    {
        return $this->stockage;
    }

    public function setStockage(?int $stockage): self
    {
        $this->stockage = $stockage;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): self
    {
        $this->remarques = $remarques;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): self
    {
        $this->marque = $marque;

        return $this;
    }
}
