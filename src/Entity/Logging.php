<?php

declare(strict_types=1);

namespace WebWMS\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'logging')]
#[ORM\Entity(repositoryClass: 'WebWMS\Repository\LoggingRepository')]
class Logging
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'route', type: 'string', length: 100, nullable: false)]
    private string $route;

    #[ORM\Column(name: 'message', type: 'string', length: 255, nullable: false)]
    private string $message;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: false)]
    private \DateTimeInterface $date;

    #[ORM\Column(name: 'user', type: 'string', length: 100, nullable: false)]
    private string $user;

    #[ORM\Column(name: 'ip_address', type: 'string', length: 20, nullable: false)]
    private string $ipAddress;

    #[ORM\Column(name: 'user_agent', type: 'string', length: 255, nullable: false)]
    private string $userAgent;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): self
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

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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
