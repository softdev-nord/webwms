<?php

declare(strict_types=1);

namespace WebWMS\Security\V3;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/** @extends Voter<string, null> */
final class PermissionVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject === null
            && preg_match('/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*){2,4}$/', $attribute) === 1;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
        ?Vote $vote = null,
    ): bool {
        $user = $token->getUser();

        return $user instanceof SecurityUser && $user->hasPermission($attribute);
    }
}
