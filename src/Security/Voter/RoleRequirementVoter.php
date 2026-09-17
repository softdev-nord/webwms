<?php

declare(strict_types=1);

namespace WebWMS\Security\Voter;

use ReflectionClass;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\Attribute\Required;
use WebWMS\Security\Attribute\RequireRole;
use Symfony\Component\HttpFoundation\Request;

class RoleRequirementVoter implements VoterInterface
{
    /**
     * Prüft RequireRole-Attribute auf Controller-Methods
     */
    public function supportsAttribute(string $attribute): bool
    {
        return $attribute === RequireRole::class;
    }

    /**
     * Gibt TRUE zurück (wird aus Attribute bestimmt)
     */
    public function supportsType(string $subjectType): bool
    {
        return true;
    }

    public function vote(TokenInterface $token, mixed $subject, array $attributes, Vote|null $vote = null): int
    {
        // RequireRole wird als EventListener/Middleware durchgesetzt
        // Diese Voter-Klasse ist Platzhalter für explizite Rolle-Enforcement
        return VoterInterface::ACCESS_ABSTAIN;
    }
}

