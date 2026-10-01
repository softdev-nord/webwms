<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\DataExchangeService;
use WebWMS\Integration\Domain\IntegrationExchangeJob;
use WebWMS\Integration\Domain\IntegrationExchangeRepository;
use WebWMS\Integration\Domain\IntegrationMapping;

class DataExchangeServiceTest extends TestCase
{
    public function testJsonImportIsValidatedAndAudited(): void
    {
        $repository = $this->createMock(IntegrationExchangeRepository::class);
        $repository->expects($this->once())->method('addJob')->with(
            self::callback(static fn (IntegrationExchangeJob $job): bool => $job->tenantId === 'tenant-id'
                && $job->format === 'json'
                && count($job->rows) === 2),
        );

        $job = new DataExchangeService($repository)->import(
            'tenant-id',
            'json',
            'product',
            '[{"sku":"A-1","stock":2},{"sku":"A-2","stock":3}]',
            'products.json',
            'user-id',
            new DateTimeImmutable('2026-10-01T06:00:00+00:00'),
        );

        self::assertCount(2, $job->rows);
    }

    public function testCsvExportProducesReusableImport(): void
    {
        $repository = $this->createMock(IntegrationExchangeRepository::class);
        $repository->expects($this->exactly(2))->method('addJob');
        $service = new DataExchangeService($repository);
        $now = new DateTimeImmutable('2026-10-01T06:00:00+00:00');

        $export = $service->export('tenant-id', 'csv', 'stock', [['sku' => 'A-1', 'quantity' => 4]], 'user-id', $now);
        $import = $service->import('tenant-id', 'csv', 'stock', $export['content'], null, 'user-id', $now);

        self::assertSame('A-1', $import->rows[0]['sku']);
        self::assertSame('4', $import->rows[0]['quantity']);
    }

    public function testErpMappingIsAppliedDuringGenericImport(): void
    {
        $repository = $this->createMock(IntegrationExchangeRepository::class);
        $repository->expects($this->once())->method('mappingsFor')->with('tenant-id', 'erp', 'product')->willReturn([
            new IntegrationMapping(
                'mapping-id',
                'tenant-id',
                'erp',
                'product',
                'ItemNo',
                'sku',
                'uppercase',
                'user-id',
                new DateTimeImmutable(),
            ),
        ]);
        $repository->expects($this->once())->method('addJob');

        $job = new DataExchangeService($repository)->import(
            'tenant-id',
            'json',
            'product',
            '[{"ItemNo":"erp-100"}]',
            'erp-products.json',
            'user-id',
            new DateTimeImmutable(),
            'erp',
        );

        self::assertSame([['sku' => 'ERP-100']], $job->rows);
    }

    public function testSapIdocRequiresControlRecordAndUsesItsIdentifiers(): void
    {
        $repository = $this->createMock(IntegrationExchangeRepository::class);
        $repository->expects($this->once())->method('addJob');
        $repository->method('mappingsFor')->willReturn([new IntegrationMapping(
            'mapping-id',
            'tenant-id',
            'sap_idoc',
            'ORDERS05',
            'BELNR',
            'orderNumber',
            'trim',
            'user-id',
            new DateTimeImmutable(),
        )]);
        $service = new DataExchangeService($repository);

        $job = $service->receiveIdoc(
            'tenant-id',
            '<IDOC><EDI_DC40><DOCNUM>4711</DOCNUM><MESTYP>ORDERS05</MESTYP></EDI_DC40><E1EDK01><BELNR>10001</BELNR></E1EDK01></IDOC>',
            'user-id',
            new DateTimeImmutable(),
        );

        self::assertSame('ORDERS05', $job->resourceType);
        self::assertSame('4711', $job->sourceReference);
        self::assertSame('10001', $job->rows[0]['orderNumber']);
    }

    public function testInvalidSapIdocIsRejectedBeforePersistence(): void
    {
        $repository = $this->createMock(IntegrationExchangeRepository::class);
        $repository->expects($this->never())->method('addJob');

        $this->expectException(\InvalidArgumentException::class);
        new DataExchangeService($repository)->receiveIdoc(
            'tenant-id',
            '<IDOC><E1EDK01><BELNR>10001</BELNR></E1EDK01></IDOC>',
            'user-id',
            new DateTimeImmutable(),
        );
    }

    public function testXlsxExportCanBeImportedAgain(): void
    {
        if (!class_exists(\ZipArchive::class)) {
            self::markTestSkipped('The ZIP extension is required for XLSX.');
        }

        $repository = $this->createMock(IntegrationExchangeRepository::class);
        $repository->expects($this->exactly(2))->method('addJob');
        $service = new DataExchangeService($repository);
        $now = new DateTimeImmutable();
        $export = $service->export('tenant-id', 'xlsx', 'product', [['sku' => 'A-1', 'name' => 'Article']], 'user-id', $now);

        $import = $service->import('tenant-id', 'xlsx', 'product', $export['content'], 'products.xlsx', 'user-id', $now);

        self::assertSame('A-1', $import->rows[0]['sku']);
        self::assertSame('Article', $import->rows[0]['name']);
    }

    public function testMappingAndCommerceConnectionAreDelegatedToRepository(): void
    {
        $repository = $this->createMock(IntegrationExchangeRepository::class);
        $repository->expects($this->once())->method('addMapping');
        $repository->expects($this->once())->method('addCommerceConnection');
        $service = new DataExchangeService($repository);
        $now = new DateTimeImmutable();

        $mapping = $service->addMapping('tenant-id', 'erp', 'PRODUCT', 'ItemNo', 'sku', 'trim', 'user-id', $now);
        $connection = $service->addCommerceConnection(
            'tenant-id',
            'Shopware Production',
            'shopware',
            'https://shop.example.com/api',
            'SHOPWARE_API_TOKEN',
            true,
            'user-id',
            $now,
        );

        self::assertSame('sku', $mapping->targetField);
        self::assertTrue($connection->active);
    }

    public function testChannelOrderRequiresPayload(): void
    {
        $repository = $this->createStub(IntegrationExchangeRepository::class);

        $this->expectException(\InvalidArgumentException::class);
        new DataExchangeService($repository)->importChannelOrder(
            'tenant-id',
            'connection-id',
            'ORDER-1',
            [],
            'user-id',
            new DateTimeImmutable(),
        );
    }

    public function testChannelOrderReturnsIdempotentRepositoryIdentifier(): void
    {
        $repository = $this->createMock(IntegrationExchangeRepository::class);
        $repository->expects($this->once())->method('addChannelOrder')->willReturn('existing-order-id');

        $id = new DataExchangeService($repository)->importChannelOrder(
            'tenant-id',
            'connection-id',
            'ORDER-1',
            ['sku' => 'A-1'],
            'user-id',
            new DateTimeImmutable(),
        );

        self::assertSame('existing-order-id', $id);
    }
}
