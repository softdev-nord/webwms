<?php

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\OrderPosRepository;

/**
 * @ORM\Entity(repositoryClass=OrderPosRepository::class)
 */
class OrderPos
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
    private $bst_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $art_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $bst_pos_menge;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBstId(): ?int
    {
        return $this->bst_id;
    }

    public function setBstId(?int $bst_id): self
    {
        $this->bst_id = $bst_id;

        return $this;
    }

    public function getArtId(): ?int
    {
        return $this->art_id;
    }

    public function setArtId(?int $art_id): self
    {
        $this->art_id = $art_id;

        return $this;
    }

    public function getBstPosMenge(): ?int
    {
        return $this->bst_pos_menge;
    }

    public function setBstPosMenge(int $bst_pos_menge): self
    {
        $this->bst_pos_menge = $bst_pos_menge;

        return $this;
    }
}
