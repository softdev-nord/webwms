<?php

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\StockRotationRepository;

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
    private $stock_location_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $art_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $usr_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $customer_order_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $supplier_order_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $movement_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $pos_quantity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $access_date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dispatch_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getStockLocationId()
    {
        return $this->stock_location_id;
    }

    /**
     * @param mixed $stock_location_id
     */
    public function setStockLocationId($stock_location_id): void
    {
        $this->stock_location_id = $stock_location_id;
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
    public function getCustomerOrderId()
    {
        return $this->customer_order_id;
    }

    /**
     * @param mixed $customer_order_id
     */
    public function setCustomerOrderId($customer_order_id): void
    {
        $this->customer_order_id = $customer_order_id;
    }

    /**
     * @return mixed
     */
    public function getSupplierOrderId()
    {
        return $this->supplier_order_id;
    }

    /**
     * @param mixed $supplier_order_id
     */
    public function setSupplierOrderId($supplier_order_id): void
    {
        $this->supplier_order_id = $supplier_order_id;
    }

    /**
     * @return mixed
     */
    public function getMovementId()
    {
        return $this->movement_id;
    }

    /**
     * @param mixed $movement_id
     */
    public function setMovementId($movement_id): void
    {
        $this->movement_id = $movement_id;
    }

    /**
     * @return mixed
     */
    public function getPosQuantity()
    {
        return $this->pos_quantity;
    }

    /**
     * @param mixed $pos_quantity
     */
    public function setPosQuantity($pos_quantity): void
    {
        $this->pos_quantity = $pos_quantity;
    }

    /**
     * @return mixed
     */
    public function getAccessDate(): ?\DateTimeInterface
    {
        return $this->access_date;
    }

    /**
     * @param mixed $access_date
     */
    public function setAccessDate(?\DateTimeInterface $access_date): void
    {
        $this->access_date = $access_date;
    }

    /**
     * @return mixed
     */
    public function getDispatchDate(): ?\DateTimeInterface
    {
        return $this->dispatch_date;
    }

    /**
     * @param mixed $dispatch_date
     */
    public function setDispatchDate(?\DateTimeInterface $dispatch_date): void
    {
        $this->dispatch_date = $dispatch_date;
    }
}
