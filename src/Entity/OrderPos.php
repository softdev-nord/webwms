<?php

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\CustomerOrderPosRepository;

/**
 * @ORM\Entity(repositoryClass=CustomerOrderPosRepository::class)
 */
class OrderPos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $orderId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $articleId;

    /**
     * @ORM\Column(type="decimal")
     */
    private ?float $orderPosQuantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId($orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    public function setArticleId($articleId): void
    {
        $this->articleId = $articleId;
    }

    public function getOrderPosQuantity(): ?float
    {
        return $this->orderPosQuantity;
    }

    public function setOrderPosQuantity($orderPosQuantity): void
    {
        $this->orderPosQuantity = $orderPosQuantity;
    }
}
