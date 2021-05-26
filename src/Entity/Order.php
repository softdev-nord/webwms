<?php

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\OrderRepository;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`orders`")
 */
class Order
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
    private $ben_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $lief_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bst_nr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bst_ref;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $bst_bst_dat;

    /**
     * @ORM\Column(type="integer")
     */
    private $bst_id;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLiefId(): ?int
    {
        return $this->lief_id;
    }

    public function setLiefId(int $lief_id): self
    {
        $this->lief_id = $lief_id;

        return $this;
    }

    public function getBstNr(): ?string
    {
        return $this->bst_nr;
    }

    public function setBstNr(string $bst_nr): self
    {
        $this->bst_nr = $bst_nr;

        return $this;
    }

    public function getBstRef(): ?string
    {
        return $this->bst_ref;
    }

    public function setBstRef(?string $bst_ref): self
    {
        $this->bst_ref = $bst_ref;

        return $this;
    }

    public function getBstBstDat(): ?\DateTimeInterface
    {
        return $this->bst_bst_dat;
    }

    public function setBstBstDat(?\DateTimeInterface $bst_bst_dat): self
    {
        $this->bst_bst_dat = $bst_bst_dat;

        return $this;
    }

    public function getBstId(): ?int
    {
        return $this->bst_id;
    }

    public function setBstId(int $bst_id): self
    {
        $this->bst_id = $bst_id;

        return $this;
    }
}
