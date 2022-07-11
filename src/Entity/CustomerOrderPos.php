<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Components\Entity\ModelEntity;
use WebWMS\Repository\CustomerOrderPosRepository;

/**
 * @ORM\Entity(repositoryClass=CustomerOrderPosRepository::class)
 * @ORM\Table(name="`customer_order_pos`")
 */
class CustomerOrderPos extends ModelEntity
{
    /**
     * @var \WebWMS\Entity\CustomerOrder
     *
     * @ORM\ManyToOne(targetEntity="\WebWMS\Entity\CustomerOrder", inversedBy="details")
     * @ORM\JoinColumn(name="customer_order_id", referencedColumnName="id")
     */
    protected $customer_orders;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="customer_order_id", type="integer", nullable=true)
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

    public function __construct()
    {
        $this->customer_orders = new ArrayCollection();
    }

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

    public function getCustomerOrders(): CustomerOrder
    {
        return $this->customer_orders;
    }

    public function setCustomerOrders(CustomerOrder $customer_orders): void
    {
        $this->customer_orders = $customer_orders;
    }
}
