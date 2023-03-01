<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Twig\Environment;
use Twig\Loader\LoaderInterface;
use WebWMS\Entity\TransportRequest;
use WebWMS\Repository\TransportHistoryRepository;
use WebWMS\Service\Stock\StockLocationService;
use WebWMS\Service\Stock\StockRotationService;
use WebWMS\Service\TransportRequestService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Dashboard
 */
class Dashboard extends AbstractController
{
    private LoaderInterface $loader;

    public function __construct(
        private Requirements $requirements,
        private Environment $twig,
        private ChartBuilderInterface $chartBuilder,
        private TransportHistoryRepository $transportHistoryRepository,
        private StockRotationService $stockRotationService,
        private TransportRequestService $transportRequestService,
        private StockLocationService $stockLocationService
    ) {
        $this->loader = $this->twig->getLoader();
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'dashboard/index_new.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Dashboard',
                'incomingGoods' => $this->getAllIncomingGoods(),
                'outgoingGoods' => $this->getAllOutgoingGoods(),
                'allTransportRequestsForChart' => $this->getAllTransportRequestsForChart(),
                'warehouseUtilization' => $this->getWarehouseUtilization(),
                'allTransportRequests' => $this->getAllTransportRequest(),
            ]
        );
    }

    /**
     * @Route("/calendar", name="calendar")
     */
    public function calendar(): Response
    {
        return $this->render('dashboard/apps-calendar.html.twig');
    }

    /**
     * @Route("/chat", name="chat")
     */
    public function chat(): Response
    {
        return $this->render('dashboard/apps-chat.html.twig');
    }

    /**
     * @Route("/lock_screen", name="chat")
     */
    public function lockScreen(): Response
    {
        return $this->render('dashboard/auth-lock-screen.html.twig');
    }

    /**
     * @Route("/login_test", name="chat")
     */
    public function loginTest(): Response
    {
        return $this->render('dashboard/auth-lock-screen.html.twig');
    }

    /**
     * @return Response|void
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function root(string $path)
    {
        if ($this->loader->exists($path . '.html.twig')) {
            if ($path == '/' || $path == 'admin') {
                exit('Admin');
            }

            return $this->render($path . '.html.twig');
        }

        throw $this->createNotFoundException('The Requested Page Not Found.');
    }

    public function getAllIncomingGoods(): Chart
    {
        $datasets = [];
        $chartType = 'TYPE_LINE';
        $repo = $this->transportHistoryRepository->findBy(['trType' => 1]);

        foreach ($repo as $data) {
            if ($data->getTrAccess() !== null) {
                $datasets[] = $data->getTrAccess()->format('d.m.Y');
            }
        }

        $dataResults = array_count_values($datasets);

        return $this->createChartForDashboard($dataResults, $chartType);
    }

    public function getAllOutgoingGoods(): Chart
    {
        $datasets = [];
        $chartType = 'TYPE_BAR';
        $repo = $this->transportHistoryRepository->findBy(['trType' => 2]);

        foreach ($repo as $data) {
            if ($data->getTrDispatch() !== null) {
                $datasets[] = $data->getTrDispatch()->format('d.m.Y');
            }
        }

        $dataResults = array_count_values($datasets);

        return $this->createChartForDashboard($dataResults, $chartType);
    }

    public function getAllTransportRequestsForChart(): Chart
    {
        $datasets = [];
        $chartType = 'TYPE_BAR';
        $repo = $this->transportHistoryRepository->findAll();

        foreach ($repo as $data) {
            if ($data->getTrType() === 1 && $data->getTrAccess() !== null) {
                $datasets[] = $data->getTrAccess()->format('d.m.Y');
            } elseif ($data->getTrType() == 2 && $data->getTrDispatch() !== null) {
                $datasets[] = $data->getTrDispatch()->format('d.m.Y');
            }
        }

        $dataResults = array_count_values($datasets);

        return $this->createChartForDashboard($dataResults, $chartType);
    }

    /**
     * @return array<int>
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getAllTransportRequest(): array
    {
        $countTrOpen = [];
        $countTrInProgress = [];
        $transportRequests = $this->transportRequestService->getAllOpenTransportRequests();

        /** @var TransportRequest $transportRequest */
        foreach ($transportRequests as $transportRequest) {
            if ($transportRequest->getTrState() === 1) {
                $countTrInProgress[] = $transportRequest->getTrState();
            } else {
                $countTrOpen[] = $transportRequest->getTrState();
            }
        }

        return [
            'TrSum' => count($transportRequests),
            'TrOpen' => count($countTrOpen),
            'TrInProgress' => count($countTrInProgress),
        ];
    }

    /**
     * @throws Exception
     * @return array<int>
     */
    public function getWarehouseUtilization(): array
    {
        $warehouseUtilization = [];

        $warehouseUtilization['allStockLocations'] = count((array)
            json_decode((string)
                $this->stockLocationService->getAllStockLocations()->getContent()
            )
        );

        $warehouseUtilization['occupiedStockLocations'] = count((array)
            json_decode((string)
                $this->stockRotationService->getAllStockRotationsWithJoin()->getContent()
            )
        );

        return $warehouseUtilization;
    }

    /**
     * @param array<int> $data
     */
    public function createChartForDashboard(array $data, string $chartType): Chart
    {
        $setChartType = '';

        switch ($chartType) {
            case 'TYPE_LINE':
                $setChartType = Chart::TYPE_LINE;

                break;
            case 'TYPE_PIE':
                $setChartType = Chart::TYPE_PIE;

                break;
            case 'TYPE_BAR':
                $setChartType = Chart::TYPE_BAR;

                break;
            case 'TYPE_BUBBLE':
                $setChartType = Chart::TYPE_BUBBLE;

                break;
            case 'TYPE_DOUGHNUT':
                $setChartType = Chart::TYPE_DOUGHNUT;

                break;
            case 'TYPE_POLAR_AREA':
                $setChartType = Chart::TYPE_POLAR_AREA;

                break;
            case 'TYPE_SCATTER':
                $setChartType = Chart::TYPE_SCATTER;

                break;
            default:
                break;
        }

        $chart = $this->chartBuilder->createChart($setChartType);
        $chart->setData([
            'datasets' => [
                [
                    'data' => $data,
                ],
            ],
        ]);
        $chart->setOptions([
            'legend' => [
                'display' => 'false',
            ],
        ]);

        return $chart;
    }
}
