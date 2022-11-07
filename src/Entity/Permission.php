<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\PermissionRepository;

/**
 * Permission
 *
 * @ORM\Table(name="permission")
 * @ORM\Entity(repositoryClass=PermissionRepository::class)
 */
class Permission
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private string $name;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @ORM\Column(name="scope", type="string", length=255, nullable=false)
     */
    private string $scope;

    /**
     * @ORM\Column(name="category", type="string", length=255, nullable=false)
     */
    private string $category;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

  /**
   * @param string $name
   *
   * @throws \Exception
   */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $scope
     */
    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @param string $category
     * @return Permission
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }
}
