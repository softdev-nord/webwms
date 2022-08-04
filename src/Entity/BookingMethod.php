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
    private mixed $menu_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private mixed $confirmation;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private mixed $movement_type;

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
    private mixed $tid_description;


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMenuId(): mixed
    {
        return $this->menu_id;
    }

    /**
     * @param mixed $menu_id
     */
    public function setMenuId(mixed $menu_id): void
    {
        $this->menu_id = $menu_id;
    }

    /**
     * @return mixed
     */
    public function getConfirmation(): mixed
    {
        return $this->confirmation;
    }

    /**
     * @param mixed $confirmation
     */
    public function setConfirmation(mixed $confirmation): void
    {
        $this->confirmation = $confirmation;
    }

    /**
     * @return mixed
     */
    public function getMovementType(): mixed
    {
        return $this->movement_type;
    }

    /**
     * @param mixed $movement_type
     */
    public function setMovementType(mixed $movement_type): void
    {
        $this->movement_type = $movement_type;
    }

    /**
     * @return mixed
     */
    public function getDescription(): mixed
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription(mixed $description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getAnsteuerung(): mixed
    {
        return $this->ansteuerung;
    }

    /**
     * @param mixed $ansteuerung
     */
    public function setAnsteuerung(mixed $ansteuerung): void
    {
        $this->ansteuerung = $ansteuerung;
    }

    /**
     * @return mixed
     */
    public function getUpload(): mixed
    {
        return $this->upload;
    }

    /**
     * @param mixed $upload
     */
    public function setUpload(mixed $upload): void
    {
        $this->upload = $upload;
    }

    /**
     * @return mixed
     */
    public function getStatistics(): mixed
    {
        return $this->statistics;
    }

    /**
     * @param mixed $statistics
     */
    public function setStatistics(mixed $statistics): void
    {
        $this->statistics = $statistics;
    }

    /**
     * @return mixed
     */
    public function getPriority(): mixed
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority(mixed $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return mixed
     */
    public function getTidDescription(): mixed
    {
        return $this->tid_description;
    }

    /**
     * @param mixed $tid_description
     */
    public function setTidDescription(mixed $tid_description): void
    {
        $this->tid_description = $tid_description;
    }
}
