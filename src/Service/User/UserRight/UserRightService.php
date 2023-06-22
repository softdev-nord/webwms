<?php

declare(strict_types=1);

namespace WebWMS\Service\User\UserRight;

use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\UserRight;
use WebWMS\Service\DataHandlers\User\UserRight\UserRightDataHandler;

/**
 * @package:    WebWMS\Service\User\UserRight
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRightService
 */
class UserRightService
{
    public function __construct(
        private readonly UserRightDataHandler $userRightDataHandler
    ) {
    }

    public function getUserRightByUserRightName(string $userRightName): ?UserRight
    {
        return $this->userRightDataHandler->getUserRightByUserRightName($userRightName);
    }

    public function getUserRightById(int $userRightId): ?UserRight
    {
        return $this->userRightDataHandler->getUserRightById($userRightId);
    }

    /**
     * @return array<int, UserRight>
     */
    public function getAllUserRights(): array
    {
        return $this->userRightDataHandler->getAllUserRights();
    }

    public function addUserRight(Request $request): void
    {
        $this->userRightDataHandler->addUserRight($request);
    }

    public function updateUserRight(Request $request): ?UserRight
    {
        return $this->userRightDataHandler->updateUserRight($request);
    }

    public function deleteUserRight(string $username): void
    {
        $this->userRightDataHandler->deleteUserRight($username);
    }
}
