<?php

declare(strict_types=1);

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
    private int $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $stockLocationId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $articleId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $usrId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $customerOrderId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $supplierOrderId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $movementId;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $posQuantity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $accessDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $dispatchDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStockLocationId(): ?int
    {
        return $this->stockLocationId;
    }

    public function setStockLocationId($stockLocationId): void
    {
        $this->stockLocationId = $stockLocationId;
    }

    public function getArticleId(): ?int
    {
        return $this->articleId;
    }

    public function setArticleId(int $articleId): self
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function getUsrId(): ?int
    {
        return $this->usrId;
    }

    public function setUsrId($usrId): void
    {
        $this->usrId = $usrId;
    }

    public function getCustomerOrderId(): ?int
    {
        return $this->customerOrderId;
    }

    public function setCustomerOrderId($customerOrderId): void
    {
        $this->customerOrderId = $customerOrderId;
    }

    public function getSupplierOrderId(): ?int
    {
        return $this->supplierOrderId;
    }

    public function setSupplierOrderId($supplierOrderId): void
    {
        $this->supplierOrderId = $supplierOrderId;
    }

    public function getMovementId(): ?int
    {
        return $this->movementId;
    }

    public function setMovementId($movementId): void
    {
        $this->movementId = $movementId;
    }

    public function getPosQuantity(): ?int
    {
        return $this->posQuantity;
    }

    public function setPosQuantity($posQuantity): void
    {
        $this->posQuantity = $posQuantity;
    }

    public function getAccessDate(): ?\DateTimeInterface
    {
        return $this->accessDate;
    }

    public function setAccessDate(?\DateTimeInterface $accessDate): void
    {
        $this->accessDate = $accessDate;
    }

    public function getDispatchDate(): ?\DateTimeInterface
    {
        return $this->dispatchDate;
    }

    public function setDispatchDate(?\DateTimeInterface $dispatchDate): void
    {
        $this->dispatchDate = $dispatchDate;
    }
}
