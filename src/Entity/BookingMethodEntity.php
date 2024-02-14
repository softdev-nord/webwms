<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\BookingMethodRepository;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        BookingMethodEntity
 */
#[ORM\Table(name: 'booking_method')]
#[ORM\Entity(repositoryClass: BookingMethodRepository::class)]
class BookingMethodEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'confirmation', type: Types::INTEGER, nullable: false)]
    private int $confirmation;

    #[ORM\Column(name: 'movement_type', type: Types::STRING, length: 10, nullable: false)]
    private string $movementType;

    #[ORM\Column(name: 'description', type: Types::STRING, length: 100, nullable: false)]
    private string $description;

    #[ORM\Column(name: 'ansteuerung', type: Types::INTEGER, nullable: false)]
    private int $ansteuerung;

    #[ORM\Column(name: 'upload', type: Types::INTEGER, nullable: false)]
    private int $upload;

    #[ORM\Column(name: 'statistics', type: Types::INTEGER, nullable: false)]
    private int $statistics;

    #[ORM\Column(name: 'priority', type: Types::INTEGER, nullable: false)]
    private int $priority;

    #[ORM\Column(name: 'tid_description', type: Types::INTEGER, nullable: true)]
    private ?int $tidDescription = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getConfirmation(): int
    {
        return $this->confirmation;
    }

    public function setConfirmation(int $confirmation): self
    {
        $this->confirmation = $confirmation;

        return $this;
    }

    public function getMovementType(): string
    {
        return $this->movementType;
    }

    public function setMovementType(string $movementType): self
    {
        $this->movementType = $movementType;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAnsteuerung(): int
    {
        return $this->ansteuerung;
    }

    public function setAnsteuerung(int $ansteuerung): self
    {
        $this->ansteuerung = $ansteuerung;

        return $this;
    }

    public function getUpload(): int
    {
        return $this->upload;
    }

    public function setUpload(int $upload): self
    {
        $this->upload = $upload;

        return $this;
    }

    public function getStatistics(): int
    {
        return $this->statistics;
    }

    public function setStatistics(int $statistics): self
    {
        $this->statistics = $statistics;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getTidDescription(): ?int
    {
        return $this->tidDescription;
    }

    public function setTidDescription(?int $tidDescription): self
    {
        $this->tidDescription = $tidDescription;

        return $this;
    }
}
