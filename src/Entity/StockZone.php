<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\StockZoneRepository;

/**
 * @ORM\Entity(repositoryClass=StockZoneRepository::class)
 */
class StockZone
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
     * @ORM\Column(type="string", length=255)
     */
    private $zone_description;

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

    public function getZoneDescription(): ?string
    {
        return $this->zone_description;
    }

    public function setZoneDescription(string $zone_description): self
    {
        $this->zone_description = $zone_description;

        return $this;
    }
}
