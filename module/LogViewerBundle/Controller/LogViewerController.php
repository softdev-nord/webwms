<?php

declare(strict_types=1);

namespace WebWMS\Bundles\LogViewerBundle\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Service\RequirementsService;

/**
 * @package:    WebWMS\Bundles\LogViewerBundle\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2022, SoftDev Nord
 * Class        LogViewerController
 */
class LogViewerController extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService
    ) {
    }

    #[Route('/logs', name: 'web_wms_log_viewer')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $logFiles = [];
        $logDir = $this->getParameter('kernel.logs_dir');
        $environment = $this->getParameter('kernel.environment');
        $logFiles[] = $logDir . DIRECTORY_SEPARATOR . $environment . '.log';

        return $this->render(
            '@LogViewer/index.html.twig',
            [
                'files' => array_map('basename', $logFiles),
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Logs Übersicht',
            ]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    #[Route('/logs/{file}', name: 'web_wms_log_viewer_logs')]
    public function logs(string $file, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $logFiles = [];
        $logDir = $this->getParameter('kernel.logs_dir');
        $environment = $this->getParameter('kernel.environment');
        $logFiles[] = $logDir . DIRECTORY_SEPARATOR . $environment . '.log';

        $filename = realpath($logDir . DIRECTORY_SEPARATOR . $file);
        if ($filename == '' || $filename == '0' || $filename == false || !in_array($filename, $logFiles, true)) {
            throw new NotFoundHttpException();
        }

        $qChannel = $request->query->get('channel');
        $qLevel = $request->query->get('level');
        $qDistinct = $request->query->get('distinct');

        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $channels = [];
        $messages = [];
        $logs = [];

        $lines = array_reverse((array) file($filename, FILE_SKIP_EMPTY_LINES));
        foreach ($lines as $line) {
            if ($line === false) {
                continue;
            }

            preg_match('/^\[([0-9- :T\.\+]+)\] ([a-z_]+).([A-Z]+): (.*) ([\{|\[].*[\}\]]) (\[\])$/', $line, $matches);
            $date = new DateTime($matches[1]);
            $channel = $matches[2];
            $level = strtolower($matches[3]);
            $message = $matches[4];
            $context = json_decode($matches[5]);
            if ($qChannel && $channel !== $qChannel) {
                continue;
            }

            if ($qLevel && $level !== $qLevel) {
                continue;
            }

            if ($qDistinct && in_array($message, $messages, true)) {
                continue;
            }

            $channels[] = $channel;
            $messages[] = $message;

            $logs[] = [
                'date' => $date,
                'channel' => $channel,
                'level' => $level,
                'message' => $message,
                'context' => $context,
            ];
        }

        return $this->render(
            '@LogViewer/logs.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Server Logs',
                'logs' => $logs,
                'channels' => array_unique($channels),
                'levels' => $levels,
            ]
        );
    }
}
