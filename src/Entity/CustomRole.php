<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use WebWMS\Repository\CustomRoleRepository;

/**
 * Role.
 *
 * @ORM\Entity(repositoryClass=CustomRoleRepository::class)
 */
class CustomRole extends Role
{
    /**
     * One Role has One Group.
     *
     * @ManyToOne(targetEntity="WebWMS\Entity\Group")
     * @ORM\JoinColumns({
     *   @JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     *})
     */
    protected Group $group;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return $this
     */
    public function setGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return ?Group
     */
    public function getGroup(): ?Group
    {
        return $this->group;
    }
}
