<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use WebWMS\Repository\LoggingRepository;

/**
 * @package:    WebWMS\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LoggingEntity
 */
#[ORM\Table(name: 'logging')]
#[ORM\Entity(repositoryClass: LoggingRepository::class)]
class LoggingEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'route', type: Types::STRING, length: 100, nullable: false)]
    private string $route;

    #[ORM\Column(name: 'message', type: Types::STRING, length: 255, nullable: false)]
    private string $message;

    #[ORM\Column(name: 'date', type: Types::DATETIME_MUTABLE, nullable: false)]
    private DateTimeInterface $dateTime;

    #[ORM\Column(name: 'user', type: Types::STRING, length: 100, nullable: false)]
    private string $user;

    #[ORM\Column(name: 'ip_address', type: Types::STRING, length: 20, nullable: false)]
    private string $ipAddress;

    #[ORM\Column(name: 'user_agent', type: Types::STRING, length: 255, nullable: false)]
    private string $userAgent;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->dateTime = $date;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
