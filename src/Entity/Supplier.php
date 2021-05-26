<?php

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\SupplierRepository;

/**
 * @ORM\Entity(repositoryClass=SupplierRepository::class)
 */
class Supplier
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
    private $lief_nr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lief_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lief_ans_zu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lief_str;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $lief_hnr;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $lief_land_krz;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $lief_plz;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lief_ort;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLiefNr(): ?int
    {
        return $this->lief_nr;
    }

    public function setLiefNr(int $lief_nr): self
    {
        $this->lief_nr = $lief_nr;

        return $this;
    }

    public function getLiefName(): ?string
    {
        return $this->lief_name;
    }

    public function setLiefName(string $lief_name): self
    {
        $this->lief_name = $lief_name;

        return $this;
    }

    public function getLiefAnsZu(): ?string
    {
        return $this->lief_ans_zu;
    }

    public function setLiefAnsZu(?string $lief_ans_zu): self
    {
        $this->lief_ans_zu = $lief_ans_zu;

        return $this;
    }

    public function getLiefStr(): ?string
    {
        return $this->lief_str;
    }

    public function setLiefStr(string $lief_str): self
    {
        $this->lief_str = $lief_str;

        return $this;
    }

    public function getLiefHnr(): ?string
    {
        return $this->lief_hnr;
    }

    public function setLiefHnr(string $lief_hnr): self
    {
        $this->lief_hnr = $lief_hnr;

        return $this;
    }

    public function getLiefLandKrz(): ?string
    {
        return $this->lief_land_krz;
    }

    public function setLiefLandKrz(string $lief_land_krz): self
    {
        $this->lief_land_krz = $lief_land_krz;

        return $this;
    }

    public function getLiefPlz(): ?string
    {
        return $this->lief_plz;
    }

    public function setLiefPlz(string $lief_plz): self
    {
        $this->lief_plz = $lief_plz;

        return $this;
    }

    public function getLiefOrt(): ?string
    {
        return $this->lief_ort;
    }

    public function setLiefOrt(string $lief_ort): self
    {
        $this->lief_ort = $lief_ort;

        return $this;
    }
}
