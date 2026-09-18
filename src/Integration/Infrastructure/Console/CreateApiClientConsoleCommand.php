<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Console;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(name: 'webwms:api-client:create', description: 'Create a tenant-scoped API v3 credential')]
final class CreateApiClientConsoleCommand extends Command
{
    public function __construct(
        private readonly Connection $connection
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('tenant-id', InputArgument::REQUIRED)
            ->addArgument('acting-user-id', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('permissions', InputArgument::REQUIRED, 'Comma-separated permission keys');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tenantId = (string) $input->getArgument('tenant-id');
        $actingUserId = (string) $input->getArgument('acting-user-id');
        $name = trim((string) $input->getArgument('name'));
        $permissions = array_values(array_unique(array_filter(array_map(
            'trim',
            explode(',', (string) $input->getArgument('permissions')),
        ), static fn (string $permission): bool => $permission !== '')));
        if ($name === '' || $permissions === [] || array_filter(
            $permissions,
            static fn (string $permission): bool => preg_match('/^[a-z][a-z0-9_]*(?:\.[a-z][a-z0-9_]*){2,4}$/', $permission) !== 1,
        ) !== []) {
            throw new \InvalidArgumentException('Name and valid permission keys are required.');
        }
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_tenant WHERE id = :tenantId',
            ['tenantId' => $tenantId],
        ) === false) {
            throw new \InvalidArgumentException('The tenant does not exist.');
        }
        if ($this->connection->fetchOne(
            'SELECT 1 FROM wms_user_account WHERE id = :userId AND tenant_id = :tenantId AND status = :status',
            ['userId' => $actingUserId, 'tenantId' => $tenantId, 'status' => 'active'],
        ) === false) {
            throw new \InvalidArgumentException('The acting user does not exist or is inactive.');
        }

        $clientId = Uuid::v7()->toRfc4122();
        $secret = bin2hex(random_bytes(24));
        $this->connection->insert('wms_api_client', [
            'id' => $clientId,
            'tenant_id' => $tenantId,
            'acting_user_id' => $actingUserId,
            'name' => $name,
            'secret_hash' => hash('sha256', $secret),
            'permissions' => json_encode($permissions, JSON_THROW_ON_ERROR),
            'active' => 1,
            'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s.u'),
        ]);

        $output->writeln('API credential (shown once):');
        $output->writeln($clientId . '.' . $secret);

        return Command::SUCCESS;
    }
}
