<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'group')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\GroupRepository')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'use_default_roles', type: 'boolean')]
    private bool $useDefaultRoles = true;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groups')]
    #[ORM\JoinTable(name: 'user')]
    private Collection $user;

    #[ORM\ManyToMany(targetEntity: Feature::class, mappedBy: 'groups')]
    #[ORM\JoinTable(name: 'group_feature')]
    private Collection $features;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt;

//    #[ORM\OneToMany(mappedBy: 'group', targetEntity: Survey::class)]
//    private $survey;

    public function __construct()
    {
//        $this->roles = new ArrayCollection();
        $this->features = new ArrayCollection();
        $this->user = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

//    public function getRoles(): Collection
//    {
//      return $this->roles;
//    }

//    public function addRole(Role $role): void
//    {
//      $this->roles[] = $role;
//    }

    public function addUser(User $user): void
    {
        $this->user[] = $user;
    }

    public function getUser(): Collection
    {
        return $this->user;
    }

    public function getFeatures(): Collection
    {
        return $this->features;
    }

    public function addFeature(Feature $feature): void
    {
        $this->features[] = $feature;
    }

    public function setUseDefaultRoles(bool $useDefaultRoles): void
    {
        $this->useDefaultRoles = $useDefaultRoles;
    }

    public function isUseDefaultRoles(): bool
    {
        return $this->useDefaultRoles;
    }

    public function hasFeature(Feature $feature): bool
    {
        foreach ($this->getFeatures() as $groupFeature) {
            if ($groupFeature === $feature) {
                return true;
            }
        }

        return false;
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
