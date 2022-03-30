<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\SupplierRepository;

/**
 * @ORM\Entity(repositoryClass=SupplierRepository::class)
 */
class Supplier
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
    private $supplier_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $supplier_nr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $supplier_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $supplier_address_addition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $supplier_address_street;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $supplier_address_street_nr;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $supplier_address_country_code;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $supplier_address_zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $supplier_address_city;

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
    public function getSupplierNr()
    {
        return $this->supplier_nr;
    }

    /**
     * @param mixed $supplier_nr
     */
    public function setSupplierNr($supplier_nr): void
    {
        $this->supplier_nr = $supplier_nr;
    }

    /**
     * @return mixed
     */
    public function getSupplierName()
    {
        return $this->supplier_name;
    }

    /**
     * @param mixed $supplier_name
     */
    public function setSupplierName($supplier_name): void
    {
        $this->supplier_name = $supplier_name;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressAddition()
    {
        return $this->supplier_address_addition;
    }

    /**
     * @param mixed $supplier_address_addition
     */
    public function setSupplierAddressAddition($supplier_address_addition): void
    {
        $this->supplier_address_addition = $supplier_address_addition;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressStreet()
    {
        return $this->supplier_address_street;
    }

    /**
     * @param mixed $supplier_address_street
     */
    public function setSupplierAddressStreet($supplier_address_street): void
    {
        $this->supplier_address_street = $supplier_address_street;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressStreetNr()
    {
        return $this->supplier_address_street_nr;
    }

    /**
     * @param mixed $supplier_address_street_nr
     */
    public function setSupplierAddressStreetNr($supplier_address_street_nr): void
    {
        $this->supplier_address_street_nr = $supplier_address_street_nr;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressCountryCode()
    {
        return $this->supplier_address_country_code;
    }

    /**
     * @param mixed $supplier_address_country_code
     */
    public function setSupplierAddressCountryCode($supplier_address_country_code): void
    {
        $this->supplier_address_country_code = $supplier_address_country_code;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressZipcode()
    {
        return $this->supplier_address_zipcode;
    }

    /**
     * @param mixed $supplier_address_zipcode
     */
    public function setSupplierAddressZipcode($supplier_address_zipcode): void
    {
        $this->supplier_address_zipcode = $supplier_address_zipcode;
    }

    /**
     * @return mixed
     */
    public function getSupplierAddressCity()
    {
        return $this->supplier_address_city;
    }

    /**
     * @param mixed $supplier_address_city
     */
    public function setSupplierAddressCity($supplier_address_city): void
    {
        $this->supplier_address_city = $supplier_address_city;
    }

    public function toArray(): array
    {
        return [
            'supplier_id' => $this->supplier_id,
            'supplier_nr' => $this->supplier_nr,
            'supplier_name' => $this->supplier_name,
            'supplier_address_addition' => $this->supplier_address_addition,
            'supplier_address_street' => $this->supplier_address_street,
            'supplier_address_street_nr' => $this->supplier_address_street_nr,
            'supplier_address_country_code' => $this->supplier_address_country_code,
            'supplier_address_zipcode' => $this->supplier_address_zipcode,
            'supplier_address_city' => $this->supplier_address_city,
        ];
    }

}