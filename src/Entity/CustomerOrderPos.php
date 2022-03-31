<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\CustomerOrderPosRepository;

/**
 * @ORM\Entity(repositoryClass=CustomerOrderPosRepository::class)
 * @ORM\Table(name="`customer_order_pos`")
 */
class CustomerOrderPos
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
    private $customer_order_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $article_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $customer_order_pos_quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerOrderId(): ?int
    {
        return $this->customer_order_id;
    }

    public function setCustomerOrderId(?int $customer_order_id): self
    {
        $this->customer_order_id = $customer_order_id;

        return $this;
    }

    public function getArticleId(): ?int
    {
        return $this->article_id;
    }

    public function setArticleId(?int $article_id): self
    {
        $this->article_id = $article_id;

        return $this;
    }

    public function getCustomerOrderPosQuantity(): ?int
    {
        return $this->customer_order_pos_quantity;
    }

    public function setCustomerOrderPosQuantity(int $customer_order_pos_quantity): self
    {
        $this->customer_order_pos_quantity = $customer_order_pos_quantity;

        return $this;
    }
}
