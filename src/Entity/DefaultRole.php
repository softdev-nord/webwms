<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\DefaultRoleRepository;

/**
 * DefaultRole
 *
 * @ORM\Entity(repositoryClass=DefaultRoleRepository::class)
 */
class DefaultRole extends Role
{
    const NON_MEMBER = 'non-member';
    const MEMBER = 'member';
    const ADMINISTRATOR_MEMBER = 'administrator member';
    const CONTRIBUTOR_MEMBER = 'contributor member';

    const VALID_NAMES = [
      self::NON_MEMBER,
      self::MEMBER,
      self::ADMINISTRATOR_MEMBER,
      self::CONTRIBUTOR_MEMBER
    ];


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $name
     *
     * @return $this
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
