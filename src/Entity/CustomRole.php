<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity(repositoryClass: 'WebWMS\Repository\CustomRoleRepository')]
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
    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'details')]
    #[ORM\JoinColumn(name: 'customer_order_id', referencedColumnName: 'id')]
    protected Group $group;

    public function __construct()
    {
        parent::__construct();
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
