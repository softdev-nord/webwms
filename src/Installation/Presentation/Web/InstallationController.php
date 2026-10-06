<?php

declare(strict_types=1);

namespace WebWMS\Installation\Presentation\Web;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WebWMS\Installation\Application\InstallationConfiguration;
use WebWMS\Installation\Application\InstallationManager;

#[Route('/v3/install', name: 'installation_')]
final class InstallationController extends AbstractController
{
    private const array STEPS = ['welcome', 'database', 'organization', 'administrator', 'review'];

    public function __construct(
        private readonly InstallationManager $installationManager,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[Route('/', name: 'index_trailing_slash', methods: ['GET'])]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute($this->installationManager->isInstalled() ? 'app_v3_login' : 'installation_step', ['step' => 'welcome']);
    }

    #[Route('/{step}', name: 'step', requirements: ['step' => 'welcome|database|organization|administrator|review|complete'], methods: ['GET', 'POST'])]
    public function step(Request $request, string $step): Response
    {
        if ($this->installationManager->isInstalled() && $step !== 'complete') {
            return $this->redirectToRoute('app_v3_login');
        }

        $session = $request->getSession();
        /** @var array<string, mixed> $data */
        $data = $session->get('installation.data', $this->defaults());
        $error = null;
        $errorDetail = null;
        $databaseStatus = null;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('installation.' . $step, (string) $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Invalid installation CSRF token.');
            }
            try {
                $data = array_replace($data, $this->stepData($request, $step));
                $this->validateStep($step, $data);
                $session->set('installation.data', $data);

                if ($step === 'database') {
                    $databaseStatus = $this->installationManager->inspectDatabase($this->databaseConfiguration($data));
                    if ($databaseStatus['tables'] !== 0) {
                        throw new InvalidArgumentException('installation.error.database_not_empty');
                    }
                }

                if ($step === 'review') {
                    $result = $this->installationManager->install(InstallationConfiguration::fromArray($data));
                    $session->remove('installation.data');
                    $session->set('installation.result', [
                        'locations' => $result->storageLocationCount,
                        'checks' => $result->checks,
                        'demo_data' => (bool) $data['demo_data'],
                    ]);

                    return $this->redirectToRoute('installation_step', ['step' => 'complete']);
                }

                return $this->redirectToRoute('installation_step', ['step' => self::STEPS[array_search($step, self::STEPS, true) + 1]]);
            } catch (\Throwable $exception) {
                if (str_starts_with($exception->getMessage(), 'installation.')) {
                    $error = $exception->getMessage();
                } else {
                    $error = 'installation.error.failed';
                    $errorDetail = $this->safeErrorDetail($exception);
                }
            }
        }

        return $this->render('installation/installer.html.twig', [
            'step' => $step,
            'steps' => self::STEPS,
            'data' => $data,
            'error' => $error,
            'errorDetail' => $errorDetail,
            'databaseStatus' => $databaseStatus,
            'result' => $session->get('installation.result'),
        ]);
    }

    /** @return array<string, mixed> */
    private function defaults(): array
    {
        return array_replace([
            'database_host' => 'database', 'database_port' => 3306, 'database_name' => 'webwms_v3',
            'database_user' => '', 'database_password' => '', 'database_version' => '8.0', 'demo_data' => true,
            'tenant_name' => '', 'site_code' => 'MAIN', 'site_name' => '', 'site_timezone' => 'Europe/Berlin',
            'admin_name' => '', 'admin_email' => '', 'admin_password' => '',
        ], $this->installationManager->databaseDefaults());
    }

    /** @return array<string, mixed> */
    private function stepData(Request $request, string $step): array
    {
        return match ($step) {
            'database' => [
                'database_host' => trim((string) $request->request->get('database_host')),
                'database_port' => (int) $request->request->get('database_port'),
                'database_name' => trim((string) $request->request->get('database_name')),
                'database_user' => trim((string) $request->request->get('database_user')),
                'database_password' => (string) $request->request->get('database_password'),
                'database_version' => trim((string) $request->request->get('database_version')),
                'demo_data' => $request->request->get('demo_data') === '1',
            ],
            'organization' => [
                'tenant_name' => trim((string) $request->request->get('tenant_name')),
                'site_code' => trim((string) $request->request->get('site_code')),
                'site_name' => trim((string) $request->request->get('site_name')),
                'site_timezone' => trim((string) $request->request->get('site_timezone')),
            ],
            'administrator' => [
                'admin_name' => trim((string) $request->request->get('admin_name')),
                'admin_email' => trim((string) $request->request->get('admin_email')),
                'admin_password' => (string) $request->request->get('admin_password'),
                'admin_password_confirmation' => (string) $request->request->get('admin_password_confirmation'),
            ],
            default => [],
        };
    }

    /** @param array<string, mixed> $data */
    private function validateStep(string $step, array $data): void
    {
        if ($step === 'administrator') {
            if (!filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException('installation.error.email');
            }
            if (mb_strlen((string) $data['admin_password']) < 12) {
                throw new InvalidArgumentException('installation.error.password_length');
            }
            if ($data['admin_password'] !== $data['admin_password_confirmation']) {
                throw new InvalidArgumentException('installation.error.password_confirmation');
            }
        }
        if ($step === 'organization') {
            foreach (['tenant_name', 'site_code', 'site_name', 'site_timezone'] as $field) {
                if (trim((string) ($data[$field] ?? '')) === '') {
                    throw new InvalidArgumentException('installation.error.required');
                }
            }
            if (!in_array($data['site_timezone'], timezone_identifiers_list(), true)) {
                throw new InvalidArgumentException('installation.error.timezone');
            }
        }
        if ($step === 'database') {
            $this->databaseConfiguration($data);
        } elseif ($step === 'administrator') {
            InstallationConfiguration::fromArray($data);
        }
    }

    /** @param array<string, mixed> $data */
    private function databaseConfiguration(array $data): InstallationConfiguration
    {
        return InstallationConfiguration::fromArray(array_replace($data, [
            'tenant_name' => 'pending', 'site_code' => 'PENDING', 'site_name' => 'pending',
            'site_timezone' => 'UTC', 'admin_name' => 'pending', 'admin_email' => 'pending@example.invalid',
            'admin_password' => 'pending-password',
        ]));
    }

    private function safeErrorDetail(\Throwable $exception): string
    {
        $detail = $exception->getMessage();
        $masked = preg_replace(
            '#(mysql|mariadb)://[^\s/@:]+(?::[^\s/@]*)?@#i',
            '$1://***:***@',
            $detail,
        );

        return $masked ?? $detail;
    }
}
