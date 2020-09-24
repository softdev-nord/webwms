<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
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
    private $art_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $art_nr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $art_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $art_kat;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $art_gew;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $art_ean;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $art_einh;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $art_tiefe;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $art_breite;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $art_hoehe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtId(): ?int
    {
        return $this->art_id;
    }

    public function setArtId(int $art_id): self
    {
        $this->art_id = $art_id;

        return $this;
    }

    public function getArtNr(): ?string
    {
        return $this->art_nr;
    }

    public function setArtNr(string $art_nr): self
    {
        $this->art_nr = $art_nr;

        return $this;
    }

    public function getArtName(): ?string
    {
        return $this->art_name;
    }

    public function setArtName(string $art_name): self
    {
        $this->art_name = $art_name;

        return $this;
    }

    public function getArtKat(): ?string
    {
        return $this->art_kat;
    }

    public function setArtKat(string $art_kat): self
    {
        $this->art_kat = $art_kat;

        return $this;
    }

    public function getArtGew(): ?string
    {
        return $this->art_gew;
    }

    public function setArtGew(string $art_gew): self
    {
        $this->art_gew = $art_gew;

        return $this;
    }

    public function getArtEan(): ?string
    {
        return $this->art_ean;
    }

    public function setArtEan(string $art_ean): self
    {
        $this->art_ean = $art_ean;

        return $this;
    }

    public function getArtEinh(): ?string
    {
        return $this->art_einh;
    }

    public function setArtEinh(string $art_einh): self
    {
        $this->art_einh = $art_einh;

        return $this;
    }

    public function getArtTiefe(): ?string
    {
        return $this->art_tiefe;
    }

    public function setArtTiefe(string $art_tiefe): self
    {
        $this->art_tiefe = $art_tiefe;

        return $this;
    }

    public function getArtBreite(): ?string
    {
        return $this->art_breite;
    }

    public function setArtBreite(string $art_breite): self
    {
        $this->art_breite = $art_breite;

        return $this;
    }

    public function getArtHoehe(): ?string
    {
        return $this->art_hoehe;
    }

    public function setArtHoehe(string $art_hoehe): self
    {
        $this->art_hoehe = $art_hoehe;

        return $this;
    }
}
