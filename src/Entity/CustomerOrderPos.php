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
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="\WebWMS\Entity\CustomerOrder", inversedBy="details")
     * @ORM\JoinColumn(name="customer_order_id", referencedColumnName="id")
     */
    protected ArrayCollection $customerOrders;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="customer_order_id", type="integer", nullable=true)
     */
    private ?int $customerOrderId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $articleId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $customerOrderPosQuantity;

    public function __construct()
    {
        $this->customerOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerOrderId(): ?int
    {
        return $this->customerOrderId;
    }

    public function setCustomerOrderId(?int $customerOrderId): self
    {
        $this->customerOrderId = $customerOrderId;

        return $this;
    }

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    public function setArticleId(?int $articleId): self
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function getCustomerOrderPosQuantity(): ?int
    {
        return $this->customerOrderPosQuantity;
    }

    public function setCustomerOrderPosQuantity(int $customerOrderPosQuantity): self
    {
        $this->customerOrderPosQuantity = $customerOrderPosQuantity;

        return $this;
    }

    public function getCustomerOrders(): ArrayCollection
    {
        return $this->customerOrders;
    }

    public function setCustomerOrders(ArrayCollection $customerOrders): void
    {
        $this->customerOrders = $customerOrders;
    }
}
