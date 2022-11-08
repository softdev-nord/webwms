<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\RoleRepository;

/**
 * Role.
 *
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      "CUSTOM" = CustomRole::class,
 *      "DEFAULT" = DefaultRole::class
 * })
 * @ORM\HasLifecycleCallbacks()
 */
class Role
{
    public const DEFAULT_ROLE_TYPE = DefaultRole::class;
    public const CUSTOM_ROLE_TYPE = CustomRole::class;

    public const TYPES = [
        self::DEFAULT_ROLE_TYPE,
        self::CUSTOM_ROLE_TYPE,
    ];

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private string $description;

    /**
     * @ORM\ManyToMany(targetEntity="WebWMS\Entity\Permission")
     * @ORM\JoinTable(name="role_permission",
     *       joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *       inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     *   )
     */
    private Collection $permissions;

    /**
     * @ORM\ManyToMany(targetEntity="WebWMS\Entity\User", mappedBy="groupRoles")
     */
    private Collection $user;

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
}
