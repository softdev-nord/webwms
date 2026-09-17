# ADR 0002: Runtime baseline for WebWMS 3.0

- Status: Accepted
- Date: 2026-09-17

## Decision

The target runtime is PHP 8.4 with Symfony 8.1. MariaDB 10.6 remains the
relational database baseline. RabbitMQ is used through Symfony Messenger for
asynchronous jobs and integration messages.

The application keeps server-rendered Twig pages for operational desktop views
and responsive scanner workflows. API Platform exposes versioned integration
resources. Frontend assets are consolidated during the UI foundation ticket;
legacy generated assets are not copied into new modules.

## Upgrade policy

The dependency upgrade receives its own commit and must include a regenerated
lockfile plus successful Composer validation, container compilation, PHPUnit,
PHPStan and coding-standard checks. No hand-edited lockfile is accepted.
