<?php

declare(strict_types=1);

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
    private $order_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $usr_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $supplier_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $order_nr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $order_reference;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $order_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $order_order_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
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
    public function getUsrId()
    {
        return $this->usr_id;
    }

    /**
     * @param mixed $usr_id
     */
    public function setUsrId($usr_id): void
    {
        $this->usr_id = $usr_id;
    }

    /**
     * @return mixed
     */
    public function getSupplierId()
    {
        return $this->supplier_id;
    }

    /**
     * @param mixed $supplier_id
     */
    public function setSupplierId($supplier_id): void
    {
        $this->supplier_id = $supplier_id;
    }

    /**
     * @return mixed
     */
    public function getOrderNr()
    {
        return $this->order_nr;
    }

    /**
     * @param mixed $order_nr
     */
    public function setOrderNr($order_nr): void
    {
        $this->order_nr = $order_nr;
    }

    /**
     * @return mixed
     */
    public function getOrderReference()
    {
        return $this->order_reference;
    }

    /**
     * @param mixed $order_reference
     */
    public function setOrderReference($order_reference): void
    {
        $this->order_reference = $order_reference;
    }

    /**
     * @return mixed
     */
    public function getOrderDate()
    {
        return $this->order_date;
    }

    /**
     * @param mixed $order_date
     */
    public function setOrderDate($order_date): void
    {
        $this->order_date = $order_date;
    }

    /**
     * @return mixed
     */
    public function getOrderOrderDate()
    {
        return $this->order_order_date;
    }

    /**
     * @param mixed $order_order_date
     */
    public function setOrderOrderDate($order_order_date): void
    {
        $this->order_order_date = $order_order_date;
    }
}