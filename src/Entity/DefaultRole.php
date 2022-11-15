<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'WebWMS\Repository\DefaultRoleRepository')]
class DefaultRole extends Role
{
    public const NON_MEMBER = 'non-member';
    public const MEMBER = 'member';
    public const ADMINISTRATOR_MEMBER = 'administrator member';
    public const CONTRIBUTOR_MEMBER = 'contributor member';

    public const VALID_NAMES = [
      self::NON_MEMBER,
      self::MEMBER,
      self::ADMINISTRATOR_MEMBER,
      self::CONTRIBUTOR_MEMBER,
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function setName(string $name): self
    {
        if (!in_array($name, self::VALID_NAMES)) {
            throw new \Exception('Not valid name was provided for default role');
        }

        $this->name = $name;

        return $this;
    }
}
