<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use InvalidArgumentException;
use SimpleXMLElement;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Domain\CommerceConnection;
use WebWMS\Integration\Domain\IntegrationExchangeJob;
use WebWMS\Integration\Domain\IntegrationExchangeRepository;
use WebWMS\Integration\Domain\IntegrationMapping;
use ZipArchive;

readonly class DataExchangeService
{
    public function __construct(
        private IntegrationExchangeRepository $repository
    ) {
    }

    public function import(
        string $tenantId,
        string $format,
        string $resourceType,
        string $content,
        ?string $sourceReference,
        string $actorId,
        DateTimeImmutable $now,
        ?string $mappingSystem = null,
    ): IntegrationExchangeJob {
        $rows = $this->decode($format, $content);
        if ($rows === []) {
            throw new InvalidArgumentException('The import contains no data rows.');
        }
        if ($mappingSystem !== null) {
            if (!in_array($mappingSystem, ['erp', 'commerce'], true)) {
                throw new InvalidArgumentException('The import mapping system is not supported.');
            }
            $mappings = $this->repository->mappingsFor($tenantId, $mappingSystem, $resourceType);
            if ($mappings !== []) {
                $rows = array_map(fn (array $row): array => $this->applyMappings($row, $mappings), $rows);
            }
        }

        $job = new IntegrationExchangeJob(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            'import',
            $format,
            $resourceType,
            $sourceReference,
            $rows,
            $actorId,
            $now,
        );
        $this->repository->addJob($job);

        return $job;
    }

    /**
     * @param list<array<string, scalar|null>> $rows
     * @return array{job: IntegrationExchangeJob, content: string}
     */
    public function export(
        string $tenantId,
        string $format,
        string $resourceType,
        array $rows,
        string $actorId,
        DateTimeImmutable $now,
    ): array {
        $rows = $this->normalizeRows($rows);
        if ($rows === []) {
            throw new InvalidArgumentException('The export requires at least one data row.');
        }

        $job = new IntegrationExchangeJob(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            'export',
            $format,
            $resourceType,
            null,
            $rows,
            $actorId,
            $now,
        );
        $content = $this->encode($format, $rows);
        $this->repository->addJob($job);

        return ['job' => $job, 'content' => $content];
    }

    public function receiveIdoc(
        string $tenantId,
        string $xml,
        string $actorId,
        DateTimeImmutable $now,
    ): IntegrationExchangeJob {
        $document = $this->xml($xml);
        $controlRecords = $document->xpath('//EDI_DC40[1]');
        $control = is_array($controlRecords) ? ($controlRecords[0] ?? null) : null;
        if (!$control instanceof SimpleXMLElement) {
            throw new InvalidArgumentException('The SAP IDoc control record EDI_DC40 is missing.');
        }

        $messageType = trim((string) $control->MESTYP);
        $documentNumber = trim((string) $control->DOCNUM);
        if ($messageType === '' || $documentNumber === '') {
            throw new InvalidArgumentException('The SAP IDoc requires MESTYP and DOCNUM.');
        }

        $rows = $this->decodeIdoc($xml);
        $mappings = $this->repository->mappingsFor($tenantId, 'sap_idoc', $messageType);
        if ($mappings !== []) {
            $rows = array_map(fn (array $row): array => $this->applyMappings($row, $mappings), $rows);
        }
        $job = new IntegrationExchangeJob(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            'import',
            'idoc',
            $messageType,
            $documentNumber,
            $rows,
            $actorId,
            $now,
        );
        $this->repository->addJob($job);

        return $job;
    }

    public function addMapping(
        string $tenantId,
        string $systemType,
        string $messageType,
        string $sourceField,
        string $targetField,
        string $transformation,
        string $actorId,
        DateTimeImmutable $now,
    ): IntegrationMapping {
        $mapping = new IntegrationMapping(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            $systemType,
            $messageType,
            $sourceField,
            $targetField,
            $transformation,
            $actorId,
            $now,
        );
        $this->repository->addMapping($mapping);

        return $mapping;
    }

    public function addCommerceConnection(
        string $tenantId,
        string $name,
        string $channelType,
        string $endpointUrl,
        string $credentialEnv,
        bool $active,
        string $actorId,
        DateTimeImmutable $now,
    ): CommerceConnection {
        $connection = new CommerceConnection(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            $name,
            $channelType,
            rtrim($endpointUrl, '/'),
            $credentialEnv,
            $active,
            $actorId,
            $now,
        );
        $this->repository->addCommerceConnection($connection);

        return $connection;
    }

    /** @param array<string, mixed> $payload */
    public function importChannelOrder(
        string $tenantId,
        string $connectionId,
        string $externalOrderId,
        array $payload,
        string $actorId,
        DateTimeImmutable $now,
    ): string {
        if (trim($externalOrderId) === '' || $payload === []) {
            throw new InvalidArgumentException('A channel order requires an external ID and payload.');
        }

        return $this->repository->addChannelOrder(
            Uuid::v7()->toRfc4122(),
            $tenantId,
            $connectionId,
            $externalOrderId,
            $payload,
            $actorId,
            $now,
        );
    }

    /** @return list<array<string, scalar|null>> */
    private function decode(string $format, string $content): array
    {
        return match ($format) {
            'json' => $this->decodeJson($content),
            'xml' => $this->decodeXml($content),
            'csv' => $this->decodeCsv($content),
            'xlsx' => $this->decodeXlsx($content),
            'idoc' => $this->decodeIdoc($content),
            default => throw new InvalidArgumentException('The integration format is not supported.'),
        };
    }

    /** @param list<array<string, scalar|null>> $rows */
    private function encode(string $format, array $rows): string
    {
        return match ($format) {
            'json' => json_encode($rows, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
            'xml' => $this->encodeXml($rows),
            'csv' => $this->encodeCsv($rows),
            'xlsx' => $this->encodeXlsx($rows),
            default => throw new InvalidArgumentException('The export format is not supported.'),
        };
    }

    /** @return list<array<string, scalar|null>> */
    private function decodeJson(string $content): array
    {
        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded) || !array_is_list($decoded)) {
            throw new InvalidArgumentException('JSON imports must contain a list of objects.');
        }

        return $this->normalizeRows($decoded);
    }

    /** @return list<array<string, scalar|null>> */
    private function decodeXml(string $content): array
    {
        $xml = $this->xml($content);
        $rows = [];
        foreach ($xml->row as $row) {
            $values = [];
            foreach ($row->children() as $key => $value) {
                $values[$key] = (string) $value;
            }
            $rows[] = $values;
        }

        return $rows;
    }

    /** @return list<array<string, scalar|null>> */
    private function decodeCsv(string $content): array
    {
        $stream = fopen('php://temp', 'r+b');
        if ($stream === false) {
            throw new InvalidArgumentException('Unable to open the CSV stream.');
        }
        fwrite($stream, $content);
        rewind($stream);
        $headers = fgetcsv($stream, escape: '');
        if (!is_array($headers)) {
            fclose($stream);

            return [];
        }
        foreach ($headers as $header) {
            if (!is_string($header) || trim($header) === '') {
                fclose($stream);

                throw new InvalidArgumentException('Every CSV column requires a header.');
            }
        }

        $rows = [];
        while (($values = fgetcsv($stream, escape: '')) !== false) {
            if (count($values) !== count($headers)) {
                fclose($stream);

                throw new InvalidArgumentException('Every CSV row must match the header column count.');
            }
            $rows[] = array_combine($headers, $values);
        }
        fclose($stream);

        return $this->normalizeRows($rows);
    }

    /** @return list<array<string, scalar|null>> */
    private function decodeIdoc(string $content): array
    {
        $xml = $this->xml($content);
        $values = [];
        $elements = $xml->xpath('//*[not(*)]');
        foreach (is_array($elements) ? $elements : [] as $element) {
            $values[$element->getName()] = (string) $element;
        }

        return [$values];
    }

    /** @return list<array<string, scalar|null>> */
    private function decodeXlsx(string $content): array
    {
        $archive = new ZipArchive();
        $file = tempnam(sys_get_temp_dir(), 'webwms-xlsx-');
        if ($file === false || file_put_contents($file, $content) === false || $archive->open($file) !== true) {
            throw new InvalidArgumentException('The XLSX workbook cannot be opened.');
        }

        try {
            $sharedStrings = [];
            $sharedXml = $archive->getFromName('xl/sharedStrings.xml');
            if (is_string($sharedXml)) {
                $shared = $this->xml($sharedXml);
                $shared->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $items = $shared->xpath('//x:si');
                foreach (is_array($items) ? $items : [] as $item) {
                    $item->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                    $textNodes = $item->xpath('.//x:t');
                    $texts = is_array($textNodes) ? $textNodes : [];
                    $sharedStrings[] = implode('', array_map(static fn (SimpleXMLElement $text): string => (string) $text, $texts));
                }
            }
            $sheetXml = $archive->getFromName('xl/worksheets/sheet1.xml');
            if (!is_string($sheetXml)) {
                throw new InvalidArgumentException('The XLSX workbook has no first worksheet.');
            }
            $sheet = $this->xml($sheetXml);
            $sheet->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $matrix = [];
            $sheetRows = $sheet->xpath('//x:sheetData/x:row');
            foreach (is_array($sheetRows) ? $sheetRows : [] as $row) {
                $row->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                $values = [];
                $cells = $row->xpath('./x:c');
                foreach (is_array($cells) ? $cells : [] as $cell) {
                    $cell->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                    $type = (string) $cell['t'];
                    $nodes = $cell->xpath($type === 'inlineStr' ? './x:is/x:t' : './x:v');
                    $value = is_array($nodes) && isset($nodes[0]) ? (string) $nodes[0] : '';
                    $values[] = $type === 's' ? ($sharedStrings[(int) $value] ?? '') : $value;
                }
                $matrix[] = $values;
            }
        } finally {
            $archive->close();
            unlink($file);
        }

        $headers = array_shift($matrix);
        if (!is_array($headers)) {
            return [];
        }

        $rows = [];
        foreach ($matrix as $row) {
            if (count($row) !== count($headers)) {
                throw new InvalidArgumentException('Every XLSX row must match the header column count.');
            }
            $rows[] = array_combine($headers, $row);
        }

        return $this->normalizeRows($rows);
    }

    /** @param list<array<string, scalar|null>> $rows */
    private function encodeXml(array $rows): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rows/>');
        foreach ($rows as $values) {
            $row = $xml->addChild('row');
            foreach ($values as $key => $value) {
                if (preg_match('/^[A-Za-z_][A-Za-z0-9_.-]*$/', $key) !== 1) {
                    throw new InvalidArgumentException(sprintf('Field "%s" is not a valid XML element name.', $key));
                }
                $row->addChild($key, htmlspecialchars((string) $value, ENT_XML1));
            }
        }

        $content = $xml->asXML();

        return is_string($content) ? $content : throw new InvalidArgumentException('The XML export could not be generated.');
    }

    /** @param list<array<string, scalar|null>> $rows */
    private function encodeCsv(array $rows): string
    {
        $stream = fopen('php://temp', 'r+b');
        if ($stream === false) {
            throw new InvalidArgumentException('Unable to open the CSV stream.');
        }
        fputcsv($stream, array_keys($rows[0]), escape: '');
        foreach ($rows as $row) {
            fputcsv($stream, $row, escape: '');
        }
        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);

        return is_string($content) ? $content : '';
    }

    /** @param list<array<string, scalar|null>> $rows */
    private function encodeXlsx(array $rows): string
    {
        $file = tempnam(sys_get_temp_dir(), 'webwms-xlsx-');
        $archive = new ZipArchive();
        if ($file === false || $archive->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new InvalidArgumentException('The XLSX workbook cannot be created.');
        }

        $matrix = [array_keys($rows[0]), ...array_map(static fn (array $row): array => array_values($row), $rows)];
        $sheetRows = '';
        foreach ($matrix as $rowIndex => $row) {
            $cells = '';
            foreach ($row as $columnIndex => $value) {
                $reference = $this->columnName($columnIndex) . ($rowIndex + 1);
                $cells .= sprintf('<c r="%s" t="inlineStr"><is><t>%s</t></is></c>', $reference, htmlspecialchars((string) $value, ENT_XML1));
            }
            $sheetRows .= sprintf('<row r="%d">%s</row>', $rowIndex + 1, $cells);
        }
        $archive->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $archive->addFromString('_rels/.rels', '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $archive->addFromString('xl/workbook.xml', '<?xml version="1.0"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Data" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $archive->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
        $archive->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>' . $sheetRows . '</sheetData></worksheet>');
        $archive->close();
        $content = file_get_contents($file);
        unlink($file);

        return is_string($content) ? $content : throw new InvalidArgumentException('The XLSX workbook cannot be read.');
    }

    private function columnName(int $index): string
    {
        $name = '';
        do {
            $name = chr(65 + ($index % 26)) . $name;
            $index = intdiv($index, 26) - 1;
        } while ($index >= 0);

        return $name;
    }

    /**
     * @param array<string, scalar|null> $row
     * @param list<IntegrationMapping> $mappings
     * @return array<string, scalar|null>
     */
    private function applyMappings(array $row, array $mappings): array
    {
        $mapped = [];
        foreach ($mappings as $mapping) {
            $value = $row[$mapping->sourceField] ?? null;
            $mapped[$mapping->targetField] = match ($mapping->transformation) {
                'trim' => trim((string) $value),
                'uppercase' => mb_strtoupper((string) $value),
                'lowercase' => mb_strtolower((string) $value),
                'integer' => (int) $value,
                'decimal' => (float) $value,
                default => $value,
            };
        }

        return $mapped;
    }

    private function xml(string $content): SimpleXMLElement
    {
        $xml = simplexml_load_string($content, SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA);
        if (!$xml instanceof SimpleXMLElement) {
            throw new InvalidArgumentException('The XML document is invalid.');
        }

        return $xml;
    }

    /** @param array<mixed> $rows
     * @return list<array<string, scalar|null>>
     */
    private function normalizeRows(array $rows): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                throw new InvalidArgumentException('Every data row must be an object.');
            }
            $values = [];
            foreach ($row as $key => $value) {
                if (!is_string($key) || (!is_scalar($value) && $value !== null)) {
                    throw new InvalidArgumentException('Data rows may only contain named scalar values.');
                }
                $values[$key] = $value;
            }
            $normalized[] = $values;
        }

        return $normalized;
    }
}
