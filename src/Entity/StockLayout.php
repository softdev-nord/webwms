<?php

declare(strict_types=1);

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
    private int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockNr;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private ?string $stockDescription;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLevel1;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLevel2;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLevel3;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $stockLevel4;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private ?string $stockModel;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private ?string $stockTyp;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private ?string $stockLongDescription;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStockNr(): ?int
    {
        return $this->stockNr;
    }

    public function setStockNr(int $stockNr): self
    {
        $this->stockNr = $stockNr;

        return $this;
    }

    public function getStockDescription(): ?string
    {
        return $this->stockDescription;
    }

    public function setStockDescription(string $stockDescription): self
    {
        $this->stockDescription = $stockDescription;

        return $this;
    }

    public function getStockLevel1(): ?int
    {
        return $this->stockLevel1;
    }

    public function setStockLevel1(int $stockLevel1): self
    {
        $this->stockLevel1 = $stockLevel1;

        return $this;
    }

    public function getStockLevel2(): ?int
    {
        return $this->stockLevel2;
    }

    public function setStockLevel2(int $stockLevel2): self
    {
        $this->stockLevel2 = $stockLevel2;

        return $this;
    }

    public function getStockLevel3(): ?int
    {
        return $this->stockLevel3;
    }

    public function setStockLevel3(int $stockLevel3): self
    {
        $this->stockLevel3 = $stockLevel3;

        return $this;
    }

    public function getStockLevel4(): ?int
    {
        return $this->stockLevel4;
    }

    public function setStockLevel4(int $stockLevel4): self
    {
        $this->stockLevel4 = $stockLevel4;

        return $this;
    }

    public function getStockModel(): ?string
    {
        return $this->stockModel;
    }

    public function setStockModel(string $stockModel): self
    {
        $this->stockModel = $stockModel;

        return $this;
    }

    public function getStockTyp(): ?string
    {
        return $this->stockTyp;
    }

    public function setStockTyp(string $stockTyp): self
    {
        $this->stockTyp = $stockTyp;

        return $this;
    }

    public function getStockLongDescription(): ?string
    {
        return $this->stockLongDescription;
    }

    public function setStockLongDescription(string $stockLongDescription): self
    {
        $this->stockLongDescription = $stockLongDescription;

        return $this;
    }
}
