<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\StockZoneLayoutRepository;

/**
 * @ORM\Entity(repositoryClass=StockZoneLayoutRepository::class)
 */
class StockZoneLayout
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $zone_short_desc;

    /**
     * @ORM\Column(type="decimal", precision=25, scale=0)
     */
    private $from_coordinate;

    /**
     * @ORM\Column(type="integer")
     */
    private $from_stock_nr;

    /**
     * @ORM\Column(type="integer")
     */
    private $from_level1;

    /**
     * @ORM\Column(type="integer")
     */
    private $from_level2;

    /**
     * @ORM\Column(type="integer")
     */
    private $from_level3;

    /**
     * @ORM\Column(type="integer")
     */
    private $from_level4;

    /**
     * @ORM\Column(type="decimal", precision=25, scale=0)
     */
    private $to_coordinate;

    /**
     * @ORM\Column(type="integer")
     */
    private $to_level1;

    /**
     * @ORM\Column(type="integer")
     */
    private $to_level2;

    /**
     * @ORM\Column(type="integer")
     */
    private $to_level3;

    /**
     * @ORM\Column(type="integer")
     */
    private $to_level4;

    /**
     * @ORM\Column(type="integer")
     */
    private $sum_stock_loc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZoneShortDesc(): ?string
    {
        return $this->zone_short_desc;
    }

    public function setZoneShortDesc(string $zone_short_desc): self
    {
        $this->zone_short_desc = $zone_short_desc;

        return $this;
    }

    public function getFromCoordinate(): ?string
    {
        return $this->from_coordinate;
    }

    public function setFromCoordinate(string $from_coordinate): self
    {
        $this->from_coordinate = $from_coordinate;

        return $this;
    }

    public function getFromStockNr(): ?int
    {
        return $this->from_stock_nr;
    }

    public function setFromStockNr(int $from_stock_nr): self
    {
        $this->from_stock_nr = $from_stock_nr;

        return $this;
    }

    public function getFromLevel1(): ?int
    {
        return $this->from_level1;
    }

    public function setFromLevel1(int $from_level1): self
    {
        $this->from_level1 = $from_level1;

        return $this;
    }

    public function getFromLevel2(): ?int
    {
        return $this->from_level2;
    }

    public function setFromLevel2(int $from_level2): self
    {
        $this->from_level2 = $from_level2;

        return $this;
    }

    public function getFromLevel3(): ?int
    {
        return $this->from_level3;
    }

    public function setFromLevel3(int $from_level3): self
    {
        $this->from_level3 = $from_level3;

        return $this;
    }

    public function getFromLevel4(): ?int
    {
        return $this->from_level4;
    }

    public function setFromLevel4(int $from_level4): self
    {
        $this->from_level4 = $from_level4;

        return $this;
    }

    public function getToCoordinate(): ?string
    {
        return $this->to_coordinate;
    }

    public function setToCoordinate(string $to_coordinate): self
    {
        $this->to_coordinate = $to_coordinate;

        return $this;
    }

    public function getToLevel1(): ?int
    {
        return $this->to_level1;
    }

    public function setToLevel1(int $to_level1): self
    {
        $this->to_level1 = $to_level1;

        return $this;
    }

    public function getToLevel2(): ?int
    {
        return $this->to_level2;
    }

    public function setToLevel2(int $to_level2): self
    {
        $this->to_level2 = $to_level2;

        return $this;
    }

    public function getToLevel3(): ?int
    {
        return $this->to_level3;
    }

    public function setToLevel3(int $to_level3): self
    {
        $this->to_level3 = $to_level3;

        return $this;
    }

    public function getToLevel4(): ?int
    {
        return $this->to_level4;
    }

    public function setToLevel4(int $to_level4): self
    {
        $this->to_level4 = $to_level4;

        return $this;
    }

    public function getSumStockLoc(): ?int
    {
        return $this->sum_stock_loc;
    }

    public function setSumStockLoc(int $sum_stock_loc): self
    {
        $this->sum_stock_loc = $sum_stock_loc;

        return $this;
    }
}
