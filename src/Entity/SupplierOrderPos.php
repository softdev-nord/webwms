<?php

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\SupplierOrderPosRepository;

/**
 * @ORM\Entity(repositoryClass=SupplierOrderPosRepository::class)
 */
class SupplierOrderPos
{
    /**
     * @var \WebWMS\Entity\SupplierOrder
     *
     * @ORM\ManyToOne(targetEntity="SupplierOrder", inversedBy="details")
     * @ORM\JoinColumn(name="supplier_order_id", referencedColumnName="id")
     */
    protected $supplier_orders;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $supplier_order_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $article_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $supplier_order_pos_quantity;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getSupplierOrderPosQuantity()
    {
        return $this->supplier_order_pos_quantity;
    }

    /**
     * @param mixed $supplier_order_pos_quantity
     */
    public function setSupplierOrderPosQuantity($supplier_order_pos_quantity): void
    {
        $this->supplier_order_pos_quantity = $supplier_order_pos_quantity;
    }
}
