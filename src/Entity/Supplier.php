<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'supplier')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\SupplierRepository')]
#[ApiResource(
    extraProperties: [
        'standard_put' => true,
    ],
)]
class Supplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'supplier_id', type: 'integer', nullable: false)]
    private int $supplierId;

    #[ORM\Column(name: 'supplier_nr', type: 'integer', nullable: false)]
    private int $supplierNr;

    #[ORM\Column(name: 'supplier_name', type: 'string', length: 255, nullable: false)]
    private string $supplierName;

    #[ORM\Column(name: 'supplier_address_addition', type: 'string', length: 255, nullable: false)]
    private ?string $supplierAddressAddition;

    #[ORM\Column(name: 'supplier_address_street', type: 'string', length: 255, nullable: false)]
    private string $supplierAddressStreet;

    #[ORM\Column(name: 'supplier_address_street_nr', type: 'string', length: 10, nullable: false)]
    private string $supplierAddressStreetNr;

    #[ORM\Column(name: 'supplier_address_country_code', type: 'string', length: 10, nullable: false)]
    private string $supplierAddressCountryCode;

    #[ORM\Column(name: 'supplier_address_zipcode', type: 'string', length: 10, nullable: false)]
    private string $supplierAddressZipcode;

    #[ORM\Column(name: 'supplier_address_city', type: 'string', length: 255, nullable: false)]
    private string $supplierAddressCity;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    public function setSupplierId(int $supplierId): void
    {
        $this->supplierId = $supplierId;
    }

    public function getSupplierNr(): int
    {
        return $this->supplierNr;
    }

    public function setSupplierNr(int $supplierNr): void
    {
        $this->supplierNr = $supplierNr;
    }

    public function getSupplierName(): string
    {
        return $this->supplierName;
    }

    public function setSupplierName(string $supplierName): void
    {
        $this->supplierName = $supplierName;
    }

    public function getSupplierAddressAddition(): ?string
    {
        return $this->supplierAddressAddition;
    }

    public function setSupplierAddressAddition(?string $supplierAddressAddition): void
    {
        $this->supplierAddressAddition = $supplierAddressAddition;
    }

    public function getSupplierAddressStreet(): string
    {
        return $this->supplierAddressStreet;
    }

    public function setSupplierAddressStreet(string $supplierAddressStreet): void
    {
        $this->supplierAddressStreet = $supplierAddressStreet;
    }

    public function getSupplierAddressStreetNr(): string
    {
        return $this->supplierAddressStreetNr;
    }

    public function setSupplierAddressStreetNr(string $supplierAddressStreetNr): void
    {
        $this->supplierAddressStreetNr = $supplierAddressStreetNr;
    }

    public function getSupplierAddressCountryCode(): string
    {
        return $this->supplierAddressCountryCode;
    }

    public function setSupplierAddressCountryCode(string $supplierAddressCountryCode): void
    {
        $this->supplierAddressCountryCode = $supplierAddressCountryCode;
    }

    public function getSupplierAddressZipcode(): string
    {
        return $this->supplierAddressZipcode;
    }

    public function setSupplierAddressZipcode(string $supplierAddressZipcode): void
    {
        $this->supplierAddressZipcode = $supplierAddressZipcode;
    }

    public function getSupplierAddressCity(): string
    {
        return $this->supplierAddressCity;
    }

    public function setSupplierAddressCity(string $supplierAddressCity): void
    {
        $this->supplierAddressCity = $supplierAddressCity;
    }

    /**
     * @return array<string, int|string|null>
     */
    public function toArray(): array
    {
        return [
            'supplierId' => $this->supplierId,
            'supplierNr' => $this->supplierNr,
            'supplierName' => $this->supplierName,
            'supplierAddressAddition' => $this->supplierAddressAddition,
            'supplierAddressStreet' => $this->supplierAddressStreet,
            'supplierAddressStreetNr' => $this->supplierAddressStreetNr,
            'supplierAddressCountryCode' => $this->supplierAddressCountryCode,
            'supplierAddressZipcode' => $this->supplierAddressZipcode,
            'supplierAddressCity' => $this->supplierAddressCity,
        ];
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
