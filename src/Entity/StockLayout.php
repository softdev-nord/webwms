<?php

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\StockLayoutRepository;

/**
 * @ORM\Entity(repositoryClass=StockLayoutRepository::class)
 */
class StockLayout
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_nr;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $stock_description;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_level1;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_level2;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_level3;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_level4;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $stock_model;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $stock_typ;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $stock_long_description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStockNr(): ?int
    {
        return $this->stock_nr;
    }

    public function setStockNr(int $stock_nr): self
    {
        $this->stock_nr = $stock_nr;

        return $this;
    }

    public function getStockDescription(): ?string
    {
        return $this->stock_description;
    }

    public function setStockDescription(string $stock_description): self
    {
        $this->stock_description = $stock_description;

        return $this;
    }

    public function getStockLevel1(): ?int
    {
        return $this->stock_level1;
    }

    public function setStockLevel1(int $stock_level1): self
    {
        $this->stock_level1 = $stock_level1;

        return $this;
    }

    public function getStockLevel2(): ?int
    {
        return $this->stock_level2;
    }

    public function setStockLevel2(int $stock_level2): self
    {
        $this->stock_level2 = $stock_level2;

        return $this;
    }

    public function getStockLevel3(): ?int
    {
        return $this->stock_level3;
    }

    public function setStockLevel3(int $stock_level3): self
    {
        $this->stock_level3 = $stock_level3;

        return $this;
    }

    public function getStockLevel4(): ?int
    {
        return $this->stock_level4;
    }

    public function setStockLevel4(int $stock_level4): self
    {
        $this->stock_level4 = $stock_level4;

        return $this;
    }

    public function getStockModel(): ?string
    {
        return $this->stock_model;
    }

    public function setStockModel(string $stock_model): self
    {
        $this->stock_model = $stock_model;

        return $this;
    }

    public function getStockTyp(): ?string
    {
        return $this->stock_typ;
    }

    public function setStockTyp(string $stock_typ): self
    {
        $this->stock_typ = $stock_typ;

        return $this;
    }

    public function getStockLongDescription(): ?int
    {
        return $this->stock_long_description;
    }

    public function setStockLongDescription(int $stock_long_description): self
    {
        $this->stock_long_description = $stock_long_description;

        return $this;
    }
}
