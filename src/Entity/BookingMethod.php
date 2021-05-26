<?php

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
     * @ORM\Column(type="string", length=255)
     */
    private $bm_short;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bm_desc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBmShort(): ?string
    {
        return $this->bm_short;
    }

    public function setBmShort(string $bm_short): self
    {
        $this->bm_short = $bm_short;

        return $this;
    }

    public function getBmDesc(): ?string
    {
        return $this->bm_desc;
    }

    public function setBmDesc(string $bm_desc): self
    {
        $this->bm_desc = $bm_desc;

        return $this;
    }
}
