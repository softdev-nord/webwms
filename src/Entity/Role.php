<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'role')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\RoleRepository')]
class Role
{
    public const DEFAULT_ROLE_TYPE = DefaultRole::class;
    public const CUSTOM_ROLE_TYPE = CustomRole::class;

    public const TYPES = [
        self::DEFAULT_ROLE_TYPE,
        self::CUSTOM_ROLE_TYPE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    protected string $name;

    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: false)]
    protected string $description;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'role')]
    protected Collection $permissions;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groupRoles')]
    #[ORM\JoinTable(name: 'user')]
    protected Collection $user;
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

    public function getUser(): Collection
    {
        return $this->user;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): void
    {
        $this->permissions[] = $permission;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
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
