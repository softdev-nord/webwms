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
    private int $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private ?string $zoneShortDesc;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $zoneDescription;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZoneShortDesc(): ?string
    {
        return $this->zoneShortDesc;
    }

    public function setZoneShortDesc(string $zoneShortDesc): self
    {
        $this->zoneShortDesc = $zoneShortDesc;

        return $this;
    }

    public function getZoneDescription(): ?string
    {
        return $this->zoneDescription;
    }

    public function setZoneDescription(string $zoneDescription): self
    {
        $this->zoneDescription = $zoneDescription;

        return $this;
    }
}
