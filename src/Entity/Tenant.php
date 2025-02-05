<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\TenantRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025',
    class: 'Tenant'
)]
#[ORM\Table(name: 'tenant')]
#[ORM\Entity(repositoryClass: TenantRepository::class)]
class Tenant
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    ##[ORM\GeneratedValue(strategy: 'CUSTOM')]
    ##[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private string $id;

    #[ORM\Column(name: 'salutation', type: Types::STRING, length: 2, nullable: true)]
    private ?string $salutation = null;

    #[ORM\Column(name: 'firstname', type: Types::STRING, length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(name: 'lastname', type: Types::STRING, length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(name: 'company', type: Types::STRING, length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(name: 'additional_address_line', type: Types::STRING, length: 255, nullable: true)]
    private ?string $additionalAddressLine = null;

    #[ORM\Column(name: 'street', type: Types::STRING, length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(name: 'zip', type: Types::STRING, length: 10, nullable: true)]
    private ?string $zip = null;

    #[ORM\Column(name: 'country_iso', type: Types::STRING, length: 3, nullable: true)]
    private ?string $countryIso = null;

    #[ORM\Column(name: 'phone', type: Types::STRING, length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(name: 'fax', type: Types::STRING, length: 20, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(name: 'mobile', type: Types::STRING, length: 20, nullable: true)]
    private ?string $mobile = null;

    #[ORM\Column(name: 'email', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSalutation(): ?string
    {
        return $this->salutation;
    }

    public function setSalutation(?string $salutation): self
    {
        $this->salutation = $salutation;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getAdditionalAddressLine(): ?string
    {
        return $this->additionalAddressLine;
    }

    public function setAdditionalAddressLine(?string $additionalAddressLine): self
    {
        $this->additionalAddressLine = $additionalAddressLine;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getCountryIso(): ?string
    {
        return $this->countryIso;
    }

    public function setCountryIso(?string $countryIso): self
    {
        $this->countryIso = $countryIso;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
