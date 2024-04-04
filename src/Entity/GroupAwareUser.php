<?php

declare(strict_types=1);

namespace WebWMS\Entity;

interface GroupAwareUser
{
    /**
     * Gets the groups granted to the user.
     *
     * @return array<array<string>>
     */
    public function getGroups(): array;

    /**
     * Ruft die dem Benutzer zugewiesenen Gruppen ab.
     *
     * @return array<string>
     */
    public function getGroupNames(): array;

    /**
     * Gibt an, ob der Benutzer der angegebenen Gruppe angehört oder nicht.
     *
     * @param string $name Name der Gruppe
     */
    public function hasGroup(string $name): bool;
}
