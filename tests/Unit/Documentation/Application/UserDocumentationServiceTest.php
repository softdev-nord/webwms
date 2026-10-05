<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Documentation\Application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;
use WebWMS\Documentation\Application\UserDocumentationService;

class UserDocumentationServiceTest extends TestCase
{
    private UserDocumentationService $documentation;

    protected function setUp(): void
    {
        $this->documentation = new UserDocumentationService(dirname(__DIR__, 4));
    }

    public function testDiscoversAndGroupsTranslatedHandbook(): void
    {
        $groups = $this->documentation->groupedDocuments(locale: 'de');

        self::assertArrayHasKey('Grundlagen & Bedienkonzept', $groups);
        self::assertArrayHasKey('Administration & Konfiguration', $groups);
        self::assertArrayHasKey('Lager & Bestand', $groups);
        self::assertArrayHasKey('Wareneingang, Fulfillment & Versand', $groups);
        self::assertCount(count(UserDocumentationService::CHAPTERS), array_merge(...array_values($groups)));
    }

    public function testSearchReturnsChapterAnchorAndExcerpt(): void
    {
        $groups = $this->documentation->groupedDocuments('Seriennummern', 'de');
        $documents = array_merge(...array_values($groups));
        $stock = array_values(array_filter($documents, static fn (array $document): bool => $document['slug'] === 'warehouse-and-stock'))[0];

        self::assertSame('section_3', $stock['match']['anchor']);
        self::assertStringContainsString('Seriennummern', $stock['match']['excerpt']);
    }

    public function testSearchHandlesEmptyUnknownAndSpecialQueries(): void
    {
        self::assertCount(count(UserDocumentationService::CHAPTERS), array_merge(...array_values($this->documentation->groupedDocuments('', 'de'))));
        self::assertSame([], $this->documentation->groupedDocuments('definitely-unknown-value', 'de'));
        self::assertSame([], $this->documentation->groupedDocuments('<script>alert(1)</script>', 'de'));
    }

    public function testSearchIncludesConfigurationGuidance(): void
    {
        $groups = $this->documentation->groupedDocuments('Pflichtfelder', 'de');
        $documents = array_merge(...array_values($groups));

        self::assertNotSame([], $documents);
        self::assertSame('chapter-guidance', $documents[0]['match']['anchor']);
    }

    public function testSearchIndexesViewFieldsAndReturnsStableViewAnchor(): void
    {
        $groups = $this->documentation->groupedDocuments('Provider-Code', 'de');
        $documents = array_merge(...array_values($groups));
        $gettingStarted = array_values(array_filter($documents, static fn (array $document): bool => $document['slug'] === 'getting-started'))[0];

        self::assertSame('view-workspace_login', $gettingStarted['match']['anchor']);
    }

    public function testLoginViewHasStructuredCoverageForEveryBusinessField(): void
    {
        $view = $this->documentation->document('getting-started', 'de')['views'][0];

        self::assertSame('security/workspace_login.html.twig', $view['template']);
        self::assertSame(['tenant_id', 'email', 'password', 'provider'], array_column($view['fields'], 'id'));
        self::assertSame([], array_filter($view['fields'], static fn (array $field): bool => in_array('', [$field['format'], $field['example'], $field['help'], $field['effect'], $field['errors']], true)));
        self::assertSame('/assets/images/handbook/security/workspace-login.svg', $view['image']['src']);
    }

    public function testLoginTemplateAndHandbookFieldCoverageStayInSync(): void
    {
        $view = $this->documentation->document('getting-started', 'de')['views'][0];
        $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
        self::assertIsString($template);
        preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);

        $technicalFields = ['_csrf_token'];
        $templateFields = array_values(array_unique(array_diff($matches[1], $technicalFields)));
        sort($templateFields);
        $documentedFields = array_column($view['fields'], 'id');
        sort($documentedFields);

        self::assertSame($templateFields, $documentedFields, 'Every business input in the documented view needs structured field guidance.');
    }

    #[DataProvider('localeProvider')]
    public function testAdministrationViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $views = $this->documentation->document('users-roles-and-security', $locale)['views'];
        self::assertSame([
            'administration/index.html.twig',
            'administration/users.html.twig',
            'administration/user_new.html.twig',
            'administration/user_edit.html.twig',
            'administration/roles.html.twig',
            'administration/role_new.html.twig',
            'administration/role_edit.html.twig',
            'administration/api_clients.html.twig',
            'administration/api_client_new.html.twig',
            'administration/api_client_edit.html.twig',
            'administration/api_client_credential.html.twig',
        ], array_column($views, 'template'));

        $imageSources = [];
        foreach ($views as $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => !str_starts_with($name, '_'))
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));

            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertCount(count($imageSources), array_unique($imageSources), 'Every documented view needs a unique screenshot reference.');
    }

    #[DataProvider('localeProvider')]
    public function testSystemConfigurationDocumentsVisibleAndBusinessRelevantHiddenFields(string $locale): void
    {
        $views = $this->documentation->document('system-configuration', $locale)['views'];
        self::assertCount(1, $views);
        $view = $views[0];
        self::assertSame('administration/workspace.html.twig', $view['template']);

        $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
        self::assertIsString($template);
        preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
        $templateFields = array_values(array_unique(array_filter($matches[1], static fn (string $name): bool => $name !== '_token')));
        sort($templateFields);
        $documentedFields = array_column($view['fields'], 'id');
        sort($documentedFields);

        self::assertSame($templateFields, $documentedFields);
        self::assertContains('active', $documentedFields, 'Business-relevant hidden defaults must be documented.');
        self::assertContains('maximum_value', $documentedFields, 'Business-relevant hidden limits must be documented.');
        self::assertNotSame([], $view['fields'][0]['usages'], 'Shared field names must explain their form-specific usages.');
        self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
    }

    #[DataProvider('localeProvider')]
    public function testInternalTransportDocumentsAllThirteenFormsAndBusinessFields(string $locale): void
    {
        $views = $this->documentation->document('fulfillment-and-outbound', $locale)['views'];
        self::assertCount(1, $views);
        $view = $views[0];
        self::assertSame('fulfillment/transport/index.html.twig', $view['template']);

        $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
        self::assertIsString($template);
        self::assertSame(13, preg_match_all('/<form\b.*?<\/form>/is', $template));
        preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
        $templateFields = array_values(array_unique(array_filter($matches[1], static fn (string $name): bool => $name !== '_token')));
        sort($templateFields);
        $documentedFields = array_column($view['fields'], 'id');
        sort($documentedFields);

        self::assertCount(27, $documentedFields);
        self::assertSame($templateFields, $documentedFields);
        self::assertContains('station_type', $documentedFields, 'Business-relevant hidden defaults must remain documented.');
        self::assertCount(10, $view['actions']);
        self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
    }

    #[DataProvider('localeProvider')]
    public function testOperationalInboundViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $views = $this->documentation->document('inbound', $locale)['views'];
        self::assertSame([
            'inbound/index.html.twig',
            'inbound/new.html.twig',
            'inbound/show.html.twig',
            'inbound/planned.html.twig',
        ], array_column($views, 'template'));

        $expectedFormCounts = [0, 1, 1, 5];
        $imageSources = [];
        foreach ($views as $index => $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            self::assertSame($expectedFormCounts[$index], preg_match_all('/<form\b.*?<\/form>/is', $template));
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token')
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertCount(count($imageSources), array_unique($imageSources));
    }

    #[DataProvider('localeProvider')]
    public function testInboundControlCenterDocumentsAllFormsAndFields(string $locale): void
    {
        $views = $this->documentation->document('inbound', $locale)['views'];
        $view = array_values(array_filter($views, static fn (array $candidate): bool => $candidate['template'] === 'inbound/control.html.twig'))[0];
        $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
        self::assertIsString($template);
        self::assertSame(10, preg_match_all('/<form\b.*?<\/form>/is', $template));
        preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
        $templateFields = array_values(array_unique(array_filter($matches[1], static fn (string $name): bool => $name !== '_token')));
        sort($templateFields);
        $documentedFields = array_column($view['fields'], 'id');
        sort($documentedFields);

        self::assertCount(25, $documentedFields);
        self::assertSame($templateFields, $documentedFields);
        self::assertContains('purchase_order_id', $documentedFields, 'The business-relevant hidden purchase-order reference must be documented.');
        self::assertCount(10, $view['actions']);
        self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
    }

    #[DataProvider('localeProvider')]
    public function testOutboundOrderViewsHaveCompleteTemplateFieldCoverage(string $locale): void
    {
        $views = array_values(array_filter(
            $this->documentation->document('fulfillment-and-outbound', $locale)['views'],
            static fn (array $view): bool => str_starts_with($view['template'], 'outbound/order/') && $view['template'] !== 'outbound/order/control.html.twig'
        ));
        self::assertSame(['outbound/order/orders.html.twig', 'outbound/order/order_new.html.twig', 'outbound/order/order.html.twig'], array_column($views, 'template'));

        foreach ($views as $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            preg_match_all('/<(?:input|select|textarea)\\b[^>]*\\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token')
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
        }
    }

    #[DataProvider('localeProvider')]
    public function testPickingViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $views = array_values(array_filter(
            $this->documentation->document('fulfillment-and-outbound', $locale)['views'],
            static fn (array $view): bool => str_starts_with($view['template'], 'outbound/picking/')
        ));
        self::assertSame([
            'outbound/picking/index.html.twig',
            'outbound/picking/control.html.twig',
            'outbound/picking/show.html.twig',
        ], array_column($views, 'template'));

        $expectedFormCounts = [0, 3, 5];
        $imageSources = [];
        foreach ($views as $index => $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            self::assertSame($expectedFormCounts[$index], preg_match_all('/<form\b.*?<\/form>/is', $template));
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token')
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertCount(8, $views[1]['fields']);
        self::assertCount(7, $views[2]['fields']);
        self::assertCount(count($imageSources), array_unique($imageSources));
    }

    #[DataProvider('localeProvider')]
    public function testPackingViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $views = array_values(array_filter(
            $this->documentation->document('fulfillment-and-outbound', $locale)['views'],
            static fn (array $view): bool => str_starts_with($view['template'], 'outbound/packing/')
        ));
        self::assertSame([
            'outbound/packing/index.html.twig',
            'outbound/packing/show.html.twig',
        ], array_column($views, 'template'));

        $expectedFormCounts = [0, 3];
        $imageSources = [];
        foreach ($views as $index => $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            self::assertSame($expectedFormCounts[$index], preg_match_all('/<form\b.*?<\/form>/is', $template));
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token')
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertCount(6, $views[1]['fields']);
        self::assertCount(5, $views[1]['actions']);
        self::assertCount(count($imageSources), array_unique($imageSources));
    }

    #[DataProvider('localeProvider')]
    public function testRemainingOutboundViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $views = array_values(array_filter(
            $this->documentation->document('fulfillment-and-outbound', $locale)['views'],
            static fn (array $view): bool => str_starts_with($view['template'], 'outbound/shipping/')
                || str_starts_with($view['template'], 'outbound/loading/')
                || $view['template'] === 'outbound/order/control.html.twig'
        ));
        self::assertSame([
            'outbound/shipping/index.html.twig',
            'outbound/shipping/show.html.twig',
            'outbound/loading/index.html.twig',
            'outbound/loading/new.html.twig',
            'outbound/loading/show.html.twig',
            'outbound/order/control.html.twig',
        ], array_column($views, 'template'));

        $expectedFormCounts = [0, 4, 0, 1, 2, 7];
        $expectedFieldCounts = [0, 4, 0, 4, 0, 30];
        $dynamicControlFields = ['completeness_passed', 'condition_passed', 'customer_check_passed'];
        $imageSources = [];
        foreach ($views as $index => $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            self::assertSame($expectedFormCounts[$index], preg_match_all('/<form\b.*?<\/form>/is', $template));
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token' && $name !== '{{ name }}')
            )));
            if ($view['template'] === 'outbound/order/control.html.twig') {
                array_push($templateFields, ...$dynamicControlFields);
            }
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertCount($expectedFieldCounts[$index], $documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertCount(count($imageSources), array_unique($imageSources));
    }

    #[DataProvider('localeProvider')]
    public function testWarehouseStockAndSelectionViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $expectedTemplates = [
            'warehouse/overview.html.twig',
            'warehouse/stock.html.twig',
            'warehouse/occupancy.html.twig',
            'warehouse/movements.html.twig',
            'warehouse/traceability.html.twig',
            'warehouse/special_stock.html.twig',
            'warehouse/special_stock_classify.html.twig',
            'warehouse/selection_rules.html.twig',
            'warehouse/configuration_form.html.twig',
        ];
        $views = array_values(array_filter(
            $this->documentation->document('warehouse-and-stock', $locale)['views'],
            static fn (array $view): bool => in_array($view['template'], $expectedTemplates, true)
        ));
        self::assertSame($expectedTemplates, array_column($views, 'template'));

        $expectedFormCounts = [0, 1, 1, 1, 0, 0, 1, 0, 1];
        $expectedFieldCounts = [0, 1, 1, 3, 0, 0, 6, 0, 11];
        $imageSources = [];
        foreach ($views as $index => $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            self::assertSame($expectedFormCounts[$index], preg_match_all('/<form\b.*?<\/form>/is', $template));
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token')
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertCount($expectedFieldCounts[$index], $documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertNotSame([], $views[8]['fields'][0]['usages'], 'Shared configuration fields must document their usages.');
        self::assertCount(count($imageSources), array_unique($imageSources));
    }

    #[DataProvider('localeProvider')]
    public function testRemainingWarehouseViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $expectedTemplates = [
            'warehouse/topology.html.twig',
            'warehouse/topology_form.html.twig',
            'warehouse/stock_blocks.html.twig',
            'warehouse/stock_block_reasons.html.twig',
            'warehouse/stock_block_new.html.twig',
            'warehouse/stock_block_show.html.twig',
            'warehouse/inventory_count/control.html.twig',
        ];
        $views = array_values(array_filter(
            $this->documentation->document('warehouse-and-stock', $locale)['views'],
            static fn (array $view): bool => in_array($view['template'], $expectedTemplates, true)
        ));
        self::assertSame($expectedTemplates, array_column($views, 'template'));

        $expectedFormCounts = [0, 1, 0, 0, 1, 2, 11];
        $expectedFieldCounts = [0, 16, 0, 0, 9, 1, 23];
        $imageSources = [];
        foreach ($views as $index => $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            self::assertSame($expectedFormCounts[$index], preg_match_all('/<form\b.*?<\/form>/is', $template));
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token')
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertCount($expectedFieldCounts[$index], $documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertNotSame([], $views[1]['fields'][1]['usages'], 'Shared topology fields must document their usages.');
        self::assertNotSame([], $views[5]['fields'][0]['usages'], 'The shared workflow note must document both usages.');
        self::assertCount(11, $views[6]['actions']);
        self::assertCount(count($imageSources), array_unique($imageSources));
    }

    #[DataProvider('localeProvider')]
    public function testIntegrationCoreViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $expectedTemplates = [
            'integration/erp/index.html.twig',
            'integration/erp/new.html.twig',
            'integration/erp/show.html.twig',
            'integration/carrier/index.html.twig',
            'integration/carrier/new.html.twig',
            'integration/carrier/show.html.twig',
            'integration/carrier/products.html.twig',
            'integration/exchange/index.html.twig',
            'integration/exchange/import.html.twig',
            'integration/exchange/export.html.twig',
            'integration/exchange/mapping-new.html.twig',
            'integration/exchange/commerce-new.html.twig',
        ];
        $views = array_values(array_filter(
            $this->documentation->document('integrations-and-devices', $locale)['views'],
            static fn (array $view): bool => in_array($view['template'], $expectedTemplates, true)
        ));
        self::assertSame($expectedTemplates, array_column($views, 'template'));

        $expectedFormCounts = [0, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1];
        $expectedFieldCounts = [0, 4, 0, 0, 5, 0, 0, 0, 4, 3, 5, 5];
        $imageSources = [];
        foreach ($views as $index => $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            self::assertSame($expectedFormCounts[$index], preg_match_all('/<form\b.*?<\/form>/is', $template));
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token')
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertCount($expectedFieldCounts[$index], $documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertCount(12, array_unique($imageSources));
        $transformation = array_values(array_filter(
            $views[10]['fields'],
            static fn (array $field): bool => $field['id'] === 'transformation'
        ))[0];
        foreach (['copy', 'trim', 'uppercase', 'lowercase', 'integer', 'decimal'] as $option) {
            self::assertStringContainsString($option, $transformation['format']);
        }
    }

    #[DataProvider('localeProvider')]
    public function testPeripheralIntegrationViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $expectedTemplates = [
            'integration/device/index.html.twig',
            'integration/device/new.html.twig',
            'integration/device/show.html.twig',
            'integration/device/scan.html.twig',
            'integration/device/scan-show.html.twig',
            'integration/measurement/index.html.twig',
            'integration/measurement/device-new.html.twig',
            'integration/measurement/capture.html.twig',
            'integration/measurement/show.html.twig',
            'integration/printing/index.html.twig',
            'integration/printing/printer-new.html.twig',
            'integration/printing/printer-show.html.twig',
            'integration/printing/job-new.html.twig',
            'integration/printing/job-show.html.twig',
        ];
        $views = array_values(array_filter(
            $this->documentation->document('integrations-and-devices', $locale)['views'],
            static fn (array $view): bool => in_array($view['template'], $expectedTemplates, true)
        ));
        self::assertSame($expectedTemplates, array_column($views, 'template'));

        $expectedFormCounts = [0, 1, 1, 1, 0, 1, 1, 1, 0, 0, 1, 1, 1, 1];
        $expectedFieldCounts = [0, 4, 0, 8, 0, 0, 4, 10, 0, 0, 4, 0, 6, 0];
        $imageSources = [];
        foreach ($views as $index => $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            self::assertSame($expectedFormCounts[$index], preg_match_all('/<form\b.*?<\/form>/is', $template));
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token')
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertCount($expectedFieldCounts[$index], $documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertCount(14, array_unique($imageSources));
        self::assertStringContainsString('99', $views[12]['fields'][5]['format']);
        self::assertNotSame([], $views[13]['actions'], 'Print execution and retry need explicit guidance.');
    }

    #[DataProvider('localeProvider')]
    public function testAutomationAndTransportViewsHaveCompleteTemplateFieldAndImageCoverage(string $locale): void
    {
        $expectedTemplates = [
            'integration/wcs/index.html.twig',
            'integration/wcs/connection-new.html.twig',
            'integration/wcs/command-new.html.twig',
            'integration/wcs/command-show.html.twig',
            'integration/wcs/status-new.html.twig',
            'integration/automation/index.html.twig',
            'integration/automation/device-new.html.twig',
            'integration/automation/command-new.html.twig',
            'integration/automation/command-show.html.twig',
            'integration/transport/index.html.twig',
            'integration/transport/new.html.twig',
            'integration/transport/deliver.html.twig',
        ];
        $views = array_merge(
            $this->documentation->document('integrations-and-devices', $locale)['views'],
            $this->documentation->document('api-and-automation', $locale)['views'],
        );
        $views = array_values(array_filter(
            $views,
            static fn (array $view): bool => in_array($view['template'], $expectedTemplates, true)
        ));
        self::assertSame($expectedTemplates, array_column($views, 'template'));

        $expectedFieldCounts = [0, 6, 6, 2, 6, 0, 6, 6, 2, 0, 10, 2];
        $imageSources = [];
        foreach ($views as $index => $view) {
            $template = file_get_contents(dirname(__DIR__, 4) . '/templates/' . $view['template']);
            self::assertIsString($template);
            self::assertSame(1, preg_match_all('/<form\b.*?<\/form>/is', $template));
            preg_match_all('/<(?:input|select|textarea)\b[^>]*\bname="([^"]+)"/i', $template, $matches);
            $templateFields = array_values(array_unique(array_map(
                static fn (string $name): string => str_replace('[]', '', $name),
                array_filter($matches[1], static fn (string $name): bool => $name !== '_token')
            )));
            sort($templateFields);
            $documentedFields = array_column($view['fields'], 'id');
            sort($documentedFields);
            self::assertCount($expectedFieldCounts[$index], $documentedFields);
            self::assertSame($templateFields, $documentedFields, sprintf('Field coverage differs for "%s".', $view['template']));
            self::assertFileExists(dirname(__DIR__, 4) . '/public' . $view['image']['src']);
            $imageSources[] = $view['image']['src'];
        }

        self::assertCount(12, array_unique($imageSources));
        self::assertStringContainsString('300000', $views[10]['fields'][8]['format']);
        self::assertStringContainsString('JSON', $views[11]['fields'][1]['format']);
    }

    #[DataProvider('localeProvider')]
    public function testEveryChapterIsTranslated(string $locale): void
    {
        foreach (UserDocumentationService::CHAPTERS as $chapter) {
            $document = $this->documentation->document($chapter['slug'], $locale);
            self::assertNotSame('', $document['title']);
            self::assertSame(['prerequisites', 'permissions', 'fields', 'statuses', 'errors'], array_keys($document['guidance']));
            self::assertNotSame([], $document['sections']);
            self::assertSame([], array_filter($document['sections'], static fn (array $section): bool => $section['image'] === null));
        }
    }

    #[DataProvider('localeProvider')]
    public function testEveryHandbookImageDefinitionIsValidAndReferencesAnExistingAsset(string $locale): void
    {
        foreach (UserDocumentationService::CHAPTERS as $chapter) {
            $document = $this->documentation->document($chapter['slug'], $locale);
            $images = array_column($document['sections'], 'image');
            array_push($images, ...array_column($document['views'], 'image'));
            foreach ($images as $image) {
                self::assertIsArray($image);
                self::assertMatchesRegularExpression('#^/assets/images/handbook/[a-z0-9._/-]+$#i', $image['src']);
                self::assertNotSame('', trim($image['alt']));
                self::assertFileExists(dirname(__DIR__, 4) . '/public' . $image['src']);
            }
        }
    }

    public function testRuntimeUsesStructuredSectionsInsteadOfMarkdown(): void
    {
        $document = $this->documentation->document('getting-started', 'de');
        $section = $document['sections'][0];

        self::assertArrayNotHasKey('markdown', $document);
        self::assertSame('section_1', $section['id']);
        self::assertIsArray($section['paragraphs']);
        self::assertIsArray($section['steps']);
        self::assertSame('/assets/images/handbook/placeholder.svg', $section['image']['src']);
    }

    public function testPreviousAndNextNavigationAreLocalized(): void
    {
        $document = $this->documentation->document('navigation-and-lists', 'en');

        self::assertSame('Getting started and workspace', $document['previous']['title']);
        self::assertSame('Users, roles, permissions and single sign-on', $document['next']['title']);
    }

    public function testGermanAndEnglishTranslationKeysAreIdentical(): void
    {
        $root = dirname(__DIR__, 4) . '/translations/handbook.';
        $german = Yaml::parseFile($root . 'de.yaml');
        $english = Yaml::parseFile($root . 'en.yaml');

        self::assertSame($this->keys($german), $this->keys($english));
    }

    public function testCatalogueContainsNoUnknownOrUnusedStructures(): void
    {
        $root = dirname(__DIR__, 4) . '/translations/handbook.';
        foreach (['de', 'en'] as $locale) {
            $catalogue = Yaml::parseFile($root . $locale . '.yaml');
            self::assertSame(['ui', 'category', 'error', 'chapter'], array_keys($catalogue));
            self::assertSame(
                ['page_title', 'headline', 'introduction', 'search', 'search_action', 'reset_search', 'no_results', 'result_for', 'breadcrumb', 'all_chapters', 'on_this_page', 'no_subchapters', 'previous', 'next', 'navigation', 'open_navigation', 'close_navigation', 'overview', 'chapters', 'current_chapter', 'content', 'sidebar_section', 'sidebar_link', 'prerequisites', 'permissions', 'fields', 'statuses', 'errors', 'documented_views', 'navigation_path', 'displayed_information', 'fields_and_inputs', 'field_name', 'input_help', 'effect_and_errors', 'required', 'optional', 'format', 'example', 'effect', 'validation', 'actions', 'used_for'],
                array_keys($catalogue['ui'])
            );

            foreach ($catalogue['chapter'] as $chapter) {
                self::assertSame([], array_diff(array_keys($chapter), ['title', 'summary', 'sections', 'views', 'guidance']));
                self::assertSame(['prerequisites', 'permissions', 'fields', 'statuses', 'errors'], array_keys($chapter['guidance']));
                foreach ($chapter['sections'] as $section) {
                    self::assertSame([], array_diff(array_keys($section), ['title', 'paragraphs', 'steps', 'items', 'image']));
                    self::assertArrayHasKey('image', $section);
                }
            }
        }
    }

    public function testRejectsPathTraversal(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->documentation->document('../technical/api-v3', 'de');
    }

    /** @return iterable<string, array{string}> */
    public static function localeProvider(): iterable
    {
        yield 'German' => ['de'];
        yield 'English' => ['en'];
    }

    /** @param array<string, mixed> $values @return list<string> */
    private function keys(array $values, string $prefix = ''): array
    {
        $keys = [];
        foreach ($values as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;
            if (is_array($value)) {
                array_push($keys, ...$this->keys($value, $path));
            } else {
                $keys[] = $path;
            }
        }
        sort($keys);

        return $keys;
    }
}
