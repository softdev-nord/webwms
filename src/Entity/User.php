<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Repository\UserRepository;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'User'
)]
#[ORM\Table(name: 'user')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['user:read'],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'skip_null_values' => false,
                'groups' => ['user:read'],
            ]
        ),
        new Post(
            denormalizationContext: [
                'groups' => ['user:write'],
            ]
        ),
        new Put(
            denormalizationContext: [
                'groups' => ['user:write'],
            ]
        ),
        new Patch(
            denormalizationContext: [
                'groups' => ['user:write'],
            ]
        ),
        new Delete(
            denormalizationContext: [
                'groups' => ['user:write'],
            ]
        ),
    ],
    formats: ['json'],
    normalizationContext: [
        'skip_null_values' => false,
        'groups' => ['user:read'],
    ],
    denormalizationContext: [
        'groups' => ['user:write'],
    ],
)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(
    fields: ['username'],
    message: 'There is already an account with this username'
)]
class User implements UserInterface, GroupAwareUser, Stringable
{
    public const ROLE_DEFAULT = 'ROLE_USER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    #[ORM\Column(name: 'firstname', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    public ?string $firstname = null;

    #[ORM\Column(name: 'lastname', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    public ?string $lastname = null;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'username', type: Types::STRING, length: 255, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $username = null;

    /**
     * @var string[]
     */
    #[ORM\Column(name: 'roles', type: Types::JSON)]
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    #[ORM\Column(name: 'password', type: Types::STRING, length: 255)]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 255, unique: true, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(name: 'last_login', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?DateTime $lastLogin = null;

    #[ORM\Column(name: 'enabled', type: Types::BOOLEAN)]
    #[Groups(['user:read', 'user:write'])]
    private bool $enabled = false;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?DateTimeInterface $updatedAt = null;

    private ?string $plainPassword = '';

    /**
     * @var array<mixed>
     */
    #[ORM\Column(name: 'user_groups', type: Types::JSON)]
    #[Groups(['user:read', 'user:write'])]
    private array $userGroups = [];

    public function __toString(): string
    {
        return $this->getUsername();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username ?? '';
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getFirstname(): string
    {
        return $this->firstname ?? '';
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname ?? '';
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getEmail(): string
    {
        return $this->email ?? '';
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTime $time = null): void
    {
        $this->lastLogin = $time;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $boolean): void
    {
        $this->enabled = $boolean;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->password]);
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->username ?? '';
    }

    public function getPassword(): string
    {
        return $this->password ?? '';
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // Wir müssen sicherstellen, dass es mindestens eine Rolle gibt.
        $roles[] = self::ROLE_DEFAULT;

        return array_values(array_unique($roles));
    }

    public function setRoles(array $roles): void
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function isEqualTo(BaseUserInterface $baseUser): bool
    {
        if (!$baseUser instanceof self) {
            return false;
        }

        if ($this->password !== $baseUser->getPassword()) {
            return false;
        }

        return $this->username === $baseUser->getUsername();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function setSuperAdmin(bool $boolean): void
    {
        ($boolean)
            ? $this->addRole(static::ROLE_SUPER_ADMIN)
            : $this->removeRole(static::ROLE_SUPER_ADMIN);
    }

    public function hasRole(string $role): bool
    {
        return \in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function addRole(string $role): void
    {
        $role = strtoupper($role);

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function isAccountNonLocked(): bool
    {
        return true;
    }

    /**
     * @return array<string>
     */
    public function getUserGroups(): array
    {
        return $this->userGroups;
    }

    /**
     * @param array<array<string>> $userGroups
     */
    public function setUserGroups(array $userGroups): void
    {
        $this->userGroups = $userGroups;
    }

    /**
     * @return array<array<string>>
     */
    public function getGroups(): array
    {
        return $this->userGroups;
    }

    public function getGroupNames(): array
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group['group'];
        }

        return $names;
    }

    public function hasGroup(string $name): bool
    {
        return \in_array($name, $this->getGroupNames(), true);
    }
}
