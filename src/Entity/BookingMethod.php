<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\BookingMethodRepository;

/**
 * @ORM\Entity(repositoryClass=BookingMethodRepository::class)
 */
class BookingMethod
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private mixed $menuId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private mixed $confirmation;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private mixed $movementType;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private mixed $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private mixed $ansteuerung;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private mixed $upload;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private mixed $statistics;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private mixed $priority;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private mixed $tidDescription;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMenuId(): mixed
    {
        return $this->menuId;
    }

    public function setMenuId(mixed $menuId): void
    {
        $this->menuId = $menuId;
    }

    public function getConfirmation(): mixed
    {
        return $this->confirmation;
    }

    public function setConfirmation(mixed $confirmation): void
    {
        $this->confirmation = $confirmation;
    }

    public function getMovementType(): mixed
    {
        return $this->movementType;
    }

    public function setMovementType(mixed $movementType): void
    {
        $this->movementType = $movementType;
    }

    public function getDescription(): mixed
    {
        return $this->description;
    }

    public function setDescription(mixed $description): void
    {
        $this->description = $description;
    }

    public function getAnsteuerung(): mixed
    {
        return $this->ansteuerung;
    }

    public function setAnsteuerung(mixed $ansteuerung): void
    {
        $this->ansteuerung = $ansteuerung;
    }

    public function getUpload(): mixed
    {
        return $this->upload;
    }

    public function setUpload(mixed $upload): void
    {
        $this->upload = $upload;
    }

    public function getStatistics(): mixed
    {
        return $this->statistics;
    }

    public function setStatistics(mixed $statistics): void
    {
        $this->statistics = $statistics;
    }

    public function getPriority(): mixed
    {
        return $this->priority;
    }

    public function setPriority(mixed $priority): void
    {
        $this->priority = $priority;
    }

    public function getTidDescription(): mixed
    {
        return $this->tidDescription;
    }

    public function setTidDescription(mixed $tidDescription): void
    {
        $this->tidDescription = $tidDescription;
    }
}
