<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Components\Entity\ModelEntity;

#[ORM\Table(name: 'customer_orders_pos')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\CustomerOrderPosRepository')]
class CustomerOrderPos extends ModelEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'customer_order_id', type: 'integer', nullable: false)]
    private int $customerOrderId;

    #[ORM\Column(name: 'article_id', type: 'integer', nullable: false)]
    private int $articleId;

    #[ORM\Column(name: 'article_nr', type: 'string', length: 50, nullable: false)]
    private string $articleNr;

    #[ORM\Column(name: 'article_name', type: 'string', length: 255, nullable: false)]
    private string $articleName;

    #[ORM\Column(name: 'quantity', type: 'integer', nullable: false)]
    private int $quantity;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    /** Many Customer Order Positions have one Customer Order. This is the owning side. */
    #[ORM\ManyToOne(inversedBy: 'customerOrderPos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomerOrder $customerOrder = null;

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

    public function getArticleNr(): string
    {
        return $this->articleNr;
    }

    public function setArticleNr(string $articleNr): self
    {
        $this->articleNr = $articleNr;

        return $this;
    }

    public function getArticleName(): string
    {
        return $this->articleName;
    }

    public function setArticleName(string $articleName): self
    {
        $this->articleName = $articleName;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCustomerOrder(): ?CustomerOrder
    {
        return $this->customerOrder;
    }

    public function setCustomerOrder(?CustomerOrder $customerOrder): self
    {
        $this->customerOrder = $customerOrder;

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
