<?php

namespace WebWMS\Entity;

use WebWMS\Repository\OrderPosRepository;
use Doctrine\ORM\Mapping as ORM;

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
    private $order_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $article_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $order_pos_quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @param mixed $order_id
     */
    public function setOrderId($order_id): void
    {
        $this->order_id = $order_id;
    }

    /**
     * @return mixed
     */
    public function getArticleId()
    {
        return $this->article_id;
    }

    /**
     * @param mixed $article_id
     */
    public function setArticleId($article_id): void
    {
        $this->article_id = $article_id;
    }

    /**
     * @return mixed
     */
    public function getOrderPosQuantity()
    {
        return $this->order_pos_quantity;
    }

    /**
     * @param mixed $order_pos_quantity
     */
    public function setOrderPosQuantity($order_pos_quantity): void
    {
        $this->order_pos_quantity = $order_pos_quantity;
    }


}
