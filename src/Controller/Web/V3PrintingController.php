<?php

declare(strict_types=1);

namespace WebWMS\Controller\Web;

use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Integration\Application\PrintGateway;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/printing', name: 'v3_printing_')]
final class V3PrintingController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly PrintGateway $gateway,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.print_job.read')]
    public function index(): Response
    {
        $tenantId = $this->tenantUser()->tenantId();

        return $this->render('v3/integration/printing/index.html.twig', [
            'page' => 'Druckwarteschlange',
            'printers' => $this->queries->printers($tenantId),
            'jobs' => $this->queries->printJobs($tenantId),
        ]);
    }

    #[Route('/printers/new', name: 'printer_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.printer.write')]
    public function createPrinter(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_printing_printer_create');
            $user = $this->tenantUser();
            $printer = $this->gateway->registerPrinter(
                $user->tenantId(),
                $this->required($request, 'name'),
                $this->required($request, 'endpoint_url'),
                $this->required($request, 'credential_env'),
                $request->request->getBoolean('active'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'Der Drucker wurde angelegt.');

            return $this->redirectToRoute('v3_printing_printer_show', ['printerId' => $printer->id]);
        }

        return $this->render('v3/integration/printing/printer-new.html.twig', [
            'page' => 'Drucker anlegen',
        ]);
    }

    #[Route('/printers/{printerId}', name: 'printer_show', methods: ['GET'])]
    #[IsGranted('integration.printer.read')]
    public function showPrinter(string $printerId): Response
    {
        $tenantId = $this->tenantUser()->tenantId();

        return $this->render('v3/integration/printing/printer-show.html.twig', [
            'page' => 'Drucker',
            'printer' => $this->requiredPrinter($printerId),
            'jobs' => array_values(array_filter(
                $this->queries->printJobs($tenantId),
                static fn (array $job): bool => ($job['printer_id'] ?? null) === $printerId,
            )),
        ]);
    }

    #[Route('/printers/{printerId}/status', name: 'printer_status', methods: ['POST'])]
    #[IsGranted('integration.printer.write')]
    public function printerStatus(string $printerId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_printing_printer_status_' . $printerId);
        $printer = $this->requiredPrinter($printerId);
        $active = !(bool) ($printer['active'] ?? false);
        $user = $this->tenantUser();
        $this->gateway->changePrinterStatus(
            $user->tenantId(),
            $printerId,
            $active,
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', $active
            ? 'Der Drucker wurde aktiviert.'
            : 'Der Drucker wurde pausiert.');

        return $this->redirectToRoute('v3_printing_printer_show', ['printerId' => $printerId]);
    }

    #[Route('/jobs/new', name: 'job_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.print_job.write')]
    public function createJob(Request $request): Response
    {
        $user = $this->tenantUser();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_printing_job_create');
            $job = $this->gateway->queue(
                $user->tenantId(),
                $this->required($request, 'printer_id'),
                $this->required($request, 'document_type'),
                $this->required($request, 'document_reference'),
                $this->required($request, 'format'),
                $this->positiveInteger($request, 'copies'),
                $this->required($request, 'request_id'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'Der Druckauftrag wurde in die Warteschlange gestellt.');

            return $this->redirectToRoute('v3_printing_job_show', ['jobId' => $job->id]);
        }

        return $this->render('v3/integration/printing/job-new.html.twig', [
            'page' => 'Druckauftrag anlegen',
            'printers' => array_values(array_filter(
                $this->queries->printers($user->tenantId()),
                static fn (array $printer): bool => (bool) ($printer['active'] ?? false),
            )),
            'requestId' => Uuid::v7()->toRfc4122(),
        ]);
    }

    #[Route('/jobs/{jobId}', name: 'job_show', methods: ['GET'])]
    #[IsGranted('integration.print_job.read')]
    public function showJob(string $jobId): Response
    {
        return $this->render('v3/integration/printing/job-show.html.twig', [
            'page' => 'Druckauftrag',
            'job' => $this->requiredJob($jobId),
        ]);
    }

    #[Route('/jobs/{jobId}/execute', name: 'job_execute', methods: ['POST'])]
    #[IsGranted('integration.print_job.execute')]
    public function executeJob(string $jobId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_printing_job_execute_' . $jobId);
        $job = $this->requiredJob($jobId);
        if (!in_array($job['status'] ?? null, ['queued', 'failed'], true)) {
            throw new \DomainException('Nur wartende oder fehlgeschlagene Druckaufträge können ausgeführt werden.');
        }

        try {
            $this->gateway->execute($this->tenantUser()->tenantId(), $jobId, new DateTimeImmutable());
            $this->addFlash('success', 'Der Druckauftrag wurde erfolgreich ausgeführt.');
        } catch (\Throwable $exception) {
            $message = trim($exception->getMessage());
            $this->addFlash('danger', $message === ''
                ? 'Der Druckauftrag ist fehlgeschlagen.'
                : 'Der Druckauftrag ist fehlgeschlagen: ' . $message);
        }

        return $this->redirectToRoute('v3_printing_job_show', ['jobId' => $jobId]);
    }

    /** @return array<string, mixed> */
    private function requiredPrinter(string $printerId): array
    {
        $printer = $this->queries->printer($this->tenantUser()->tenantId(), $printerId);
        if ($printer === null) {
            throw $this->createNotFoundException('Der Drucker wurde nicht gefunden.');
        }

        return $printer;
    }

    /** @return array<string, mixed> */
    private function requiredJob(string $jobId): array
    {
        $job = $this->queries->printJob($this->tenantUser()->tenantId(), $jobId);
        if ($job === null) {
            throw $this->createNotFoundException('Der Druckauftrag wurde nicht gefunden.');
        }

        return $job;
    }

    private function positiveInteger(Request $request, string $field): int
    {
        $value = filter_var($request->request->get($field), FILTER_VALIDATE_INT);
        if (!is_int($value) || $value < 1) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" muss eine positive Ganzzahl sein.', $field));
        }

        return $value;
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return $value;
    }

    private function assertCsrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }

    private function tenantUser(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }
}
