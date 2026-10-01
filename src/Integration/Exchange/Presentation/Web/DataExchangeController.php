<?php

declare(strict_types=1);

namespace WebWMS\Integration\Exchange\Presentation\Web;

use DateTimeImmutable;
use InvalidArgumentException;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WebWMS\Integration\Application\DataExchangeService;
use WebWMS\Integration\Application\Query\IntegrationQueryService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/data-exchange', name: 'v3_data_exchange_')]
class DataExchangeController extends AbstractController
{
    public function __construct(
        private readonly DataExchangeService $exchange,
        private readonly IntegrationQueryService $queries,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.exchange.read')]
    public function index(): Response
    {
        $tenantId = $this->user()->tenantId();

        return $this->render('integration/exchange/index.html.twig', [
            'page' => 'integration.exchange.page.overview',
            'jobs' => $this->queries->exchangeJobs($tenantId),
            'mappings' => $this->queries->integrationMappings($tenantId),
            'connections' => $this->queries->commerceConnections($tenantId),
            'orders' => $this->queries->channelOrders($tenantId),
        ]);
    }

    #[Route('/import', name: 'import', methods: ['GET', 'POST'])]
    #[IsGranted('integration.exchange.write')]
    public function import(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_data_exchange_import');
            $file = $request->files->get('file');
            if (!$file instanceof UploadedFile) {
                throw new InvalidArgumentException('An import file is required.');
            }
            $content = file_get_contents($file->getPathname());
            if (!is_string($content)) {
                throw new InvalidArgumentException('The import file cannot be read.');
            }
            $format = $this->required($request, 'format');
            $user = $this->user();
            if ($format === 'idoc') {
                $this->exchange->receiveIdoc($user->tenantId(), $content, $user->actorId(), new DateTimeImmutable());
            } else {
                $this->exchange->import(
                    $user->tenantId(),
                    $format,
                    $this->required($request, 'resource_type'),
                    $content,
                    $file->getClientOriginalName(),
                    $user->actorId(),
                    new DateTimeImmutable(),
                    $this->optional($request, 'mapping_system'),
                );
            }
            $this->addFlash('success', 'integration.exchange.flash.import_completed');

            return $this->redirectToRoute('v3_data_exchange_index');
        }

        return $this->render('integration/exchange/import.html.twig', [
            'page' => 'integration.exchange.page.import',
        ]);
    }

    #[Route('/export', name: 'export', methods: ['GET', 'POST'])]
    #[IsGranted('integration.exchange.write')]
    public function export(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_data_exchange_export');
            $rows = json_decode($this->required($request, 'rows'), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($rows) || !array_is_list($rows)) {
                throw new InvalidArgumentException('Export data must be a JSON list.');
            }
            $format = $this->required($request, 'format');
            $user = $this->user();
            $result = $this->exchange->export(
                $user->tenantId(),
                $format,
                $this->required($request, 'resource_type'),
                $rows,
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $mimeType = match ($format) {
                'json' => 'application/json',
                'xml' => 'application/xml',
                'csv' => 'text/csv',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                default => 'application/octet-stream',
            };

            return new Response($result['content'], Response::HTTP_OK, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => HeaderUtils::makeDisposition('attachment', 'webwms-export.' . $format),
            ]);
        }

        return $this->render('integration/exchange/export.html.twig', [
            'page' => 'integration.exchange.page.export',
        ]);
    }

    #[Route('/mappings/new', name: 'mapping_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.exchange.write')]
    public function mapping(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_data_exchange_mapping');
            $user = $this->user();
            $this->exchange->addMapping(
                $user->tenantId(),
                $this->required($request, 'system_type'),
                $this->required($request, 'message_type'),
                $this->required($request, 'source_field'),
                $this->required($request, 'target_field'),
                $this->required($request, 'transformation'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'integration.exchange.flash.mapping_created');

            return $this->redirectToRoute('v3_data_exchange_index');
        }

        return $this->render('integration/exchange/mapping-new.html.twig', [
            'page' => 'integration.exchange.page.mapping',
        ]);
    }

    #[Route('/commerce-connections/new', name: 'commerce_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.exchange.write')]
    public function commerce(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_data_exchange_commerce');
            $user = $this->user();
            $this->exchange->addCommerceConnection(
                $user->tenantId(),
                $this->required($request, 'name'),
                $this->required($request, 'channel_type'),
                $this->required($request, 'endpoint_url'),
                $this->required($request, 'credential_env'),
                $request->request->getBoolean('active'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'integration.exchange.flash.commerce_created');

            return $this->redirectToRoute('v3_data_exchange_index');
        }

        return $this->render('integration/exchange/commerce-new.html.twig', [
            'page' => 'integration.exchange.page.commerce',
        ]);
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new InvalidArgumentException(sprintf('Field "%s" is required.', $field));
        }

        return $value;
    }

    private function optional(Request $request, string $field): ?string
    {
        $value = trim((string) $request->request->get($field));

        return $value === '' ? null : $value;
    }

    private function assertCsrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('The form token is invalid.');
        }
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }
}
