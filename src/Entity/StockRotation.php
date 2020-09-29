<?php

namespace App\Entity;

use App\Repository\StockRotationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StockRotationRepository::class)
 */
class StockRotation
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
    private $lpz_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $art_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ben_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aft_pos_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $bst_pos_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ba_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $lbw_menge;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lbw_zu_datum;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lbw_ab_datum;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLpzId(): ?int
    {
        return $this->lpz_id;
    }

    public function setLpzId(int $lpz_id): self
    {
        $this->lpz_id = $lpz_id;

        return $this;
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

    public function getBenId(): ?int
    {
        return $this->ben_id;
    }

    public function setBenId(int $ben_id): self
    {
        $this->ben_id = $ben_id;

        return $this;
    }

    public function getAftPosId(): ?int
    {
        return $this->aft_pos_id;
    }

    public function setAftPosId(int $aft_pos_id): self
    {
        $this->aft_pos_id = $aft_pos_id;

        return $this;
    }

    public function getBstPosId(): ?int
    {
        return $this->bst_pos_id;
    }

    public function setBstPosId(int $bst_pos_id): self
    {
        $this->bst_pos_id = $bst_pos_id;

        return $this;
    }

    public function getBaId(): ?int
    {
        return $this->ba_id;
    }

    public function setBaId(int $ba_id): self
    {
        $this->ba_id = $ba_id;

        return $this;
    }

    public function getLbwMenge(): ?int
    {
        return $this->lbw_menge;
    }

    public function setLbwMenge(int $lbw_menge): self
    {
        $this->lbw_menge = $lbw_menge;

        return $this;
    }

    public function getLbwZuDatum(): ?\DateTimeInterface
    {
        return $this->lbw_zu_datum;
    }

    public function setLbwZuDatum(?\DateTimeInterface $lbw_zu_datum): self
    {
        $this->lbw_zu_datum = $lbw_zu_datum;

        return $this;
    }

    public function getLbwAbDatum(): ?\DateTimeInterface
    {
        return $this->lbw_ab_datum;
    }

    public function setLbwAbDatum(?\DateTimeInterface $lbw_ab_datum): self
    {
        $this->lbw_ab_datum = $lbw_ab_datum;

        return $this;
    }
}
