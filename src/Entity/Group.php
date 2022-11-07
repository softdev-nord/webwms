<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\GroupRepository;

/**
 * Group
 *
 * @ORM\Table(name="`group`")
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 */
class Group
{
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
     * @ORM\Column(name="use_default_roles", type="boolean")
     */
    private bool $useDefaultRoles = true;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="groups")
     */
    private Collection $user;

    /**
     * @ORM\ManyToMany(targetEntity="WebWMS\Entity\Feature")
     * @ORM\JoinTable(name="group_feature",
     *   joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="feature_id", referencedColumnName="id")}
     *   )
     */
    private Collection $features;

    /**
     * @ORM\OneToMany(targetEntity="WebWMS\Entity\Survey", mappedBy="group")
     */
    private $survey;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->features = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

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
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

  public function getRoles(): Collection
  {
      return $this->roles;
  }

  public function addRole(Role $role): void
  {
      $this->roles[] = $role;
  }

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

    /**
     * @param bool $useDefaultRoles
     */
    public function setUseDefaultRoles(bool $useDefaultRoles): void
    {
        $this->useDefaultRoles = $useDefaultRoles;
    }

    /**
     * @return bool
     */
    public function isUseDefaultRoles(): bool
    {
        return $this->useDefaultRoles;
    }

    /**
     * @param Feature $feature
     *
     * @return bool
     */
    public function hasFeature(Feature $feature): bool
    {
        foreach ($this->getFeatures() as $groupFeature) {
            if ($groupFeature === $feature) {
                return true;
            }
        }

        return false;
    }
}
