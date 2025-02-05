<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use DateTime;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Entity',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'UserInterface'
)]
interface UserInterface extends PasswordAuthenticatedUserInterface, BaseUserInterface, EquatableInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';

    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Gibt die Kennung für diesen Benutzer zurück (z. B. seinen Benutzernamen oder seine E-Mail-Adresse).
     */
    public function getUserIdentifier(): string;

    /**
     * Ermittelt den Benutzernamen. Dieser wird nicht mehr von der übergeordneten Schnittstelle bereitgestellt
     * (ersetzt durch getUserIdentifier()), aber Benutzer haben immer noch Benutzernamen.
     */
    public function getUsername(): string;

    /**
     * Setzt den Benutzernamen.
     */
    public function setUsername(string $username): void;

    /**
     * Setzt die E-Mail-Adresse.
     */
    public function getEmail(): string;

    /**
     * Setzt die E-Mail-Adresse.
     */
    public function setEmail(string $email): void;

    /**
     * Ermittelt das einfache Passwort.
     */
    public function getPlainPassword(): ?string;

    /**
     * Setzt das einfache Passwort.
     */
    public function setPlainPassword(?string $plainPassword): void;

    /**
     * Setzt das gehashte Passwort.
     */
    public function setPassword(string $password): void;

    /**
     * Gibt an, ob der angegebene Benutzer die Superadmin-Rolle hat.
     */
    public function isSuperAdmin(): bool;

    /**
     * Setzt die Superadmin-Rolle.
     */
    public function setSuperAdmin(bool $boolean): void;

    /**
     * Setzt die letzte Anmeldezeit.
     */
    public function setLastLogin(?DateTime $time = null): void;

    /**
     * Verwenden Sie dies niemals, um zu prüfen, ob dieser Benutzer Zugriff auf irgendetwas hat!
     *
     * Verwenden Sie den AuthorizationChecker oder eine
     * Implementierung von AccessDecisionManager stattdessen, z.B.
     *
     *      $authorizationChecker->isGranted('ROLE_USER');
     */
    //    public function hasRole(string $role): bool;

    /**
     * Legt die Rollen des Benutzers fest.
     *
     * Dies überschreibt alle vorherigen Rollen.
     *
     * @param string[] $roles
     */
    public function setRoles(array $roles): void;

    /**
     * Fügt dem Benutzer eine Rolle hinzu.
     */
    public function addRole(string $role): void;

    /**
     * Entfernt eine Rolle vom Benutzer.
     */
    public function removeRole(string $role): void;

    /**
     * Prüft, ob der Benutzer gesperrt ist.
     *
     * Wenn diese Methode ein false zurückgibt, löst das Authentifizierungssystem
     * intern eine LockedException aus und verhindert die Anmeldung.
     *
     * @return bool true, wenn der Benutzer nicht gesperrt ist, sonst false
     *
     * @see LockedException
     */
    public function isAccountNonLocked(): bool;

    /**
     * Prüft, ob der Benutzer aktiviert ist.
     *
     * Wenn diese Methode ein false zurückgibt, wird das Authentifizierungssystem
     * eine DisabledException auslösen und die Anmeldung verhindern.
     *
     * @return bool true, wenn der Benutzer aktiviert ist, sonst false
     *
     * @see DisabledException
     */
    public function isEnabled(): bool;

    public function setEnabled(bool $boolean): void;
}
