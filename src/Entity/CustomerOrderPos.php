<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Components\Entity\ModelEntity;

#[ORM\Table(name: 'customer_orders')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\CustomerOrderRepository')]
class CustomerOrderPos extends ModelEntity
{
    /** Many Customer Order Positions have one Customer Order. This is the owning side. */
    #[ORM\ManyToOne(targetEntity: CustomerOrder::class, inversedBy: 'details')]
    #[ORM\JoinColumn(name: 'customer_order_id', referencedColumnName: 'id')]
    protected Collection $customerOrders;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'customer_order_id', type: 'integer', nullable: false)]
    private int $customerOrderId;

    #[ORM\Column(name: 'article_id', type: 'integer', nullable: false)]
    private int $articleId;

    #[ORM\Column(name: 'customer_order_pos_quantity', type: 'integer', nullable: false)]
    private int $customerOrderPosQuantity;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->customerOrders = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCustomerOrderId(): int
    {
        return $this->customerOrderId;
    }

    public function setCustomerOrderId(int $customerOrderId): self
    {
        $this->customerOrderId = $customerOrderId;

        return $this;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): self
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function getCustomerOrderPosQuantity(): int
    {
        return $this->customerOrderPosQuantity;
    }

    public function setCustomerOrderPosQuantity(int $customerOrderPosQuantity): self
    {
        $this->customerOrderPosQuantity = $customerOrderPosQuantity;

        return $this;
    }

    public function getCustomerOrders(): Collection
    {
        return $this->customerOrders;
    }

    public function setCustomerOrders(Collection $customerOrders): self
    {
        $this->customerOrders = $customerOrders;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
