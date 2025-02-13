<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\TransportHistory;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebWMS\Dto\StockInFinalDto;
use WebWMS\Entity\TransportHistory;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DateTimeService;
use WebWMS\Service\Stock\StockOccupancyService;

#[ClassInformation(
    package: 'WebWMS\Service\DataHandlers\TransportHistory',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'TransportHistoryDataHandler'
)]
readonly class TransportHistoryDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StockOccupancyService $stockOccupancyService,
        private DateTimeService $dateTimeService,
    ) {
    }

    public function save(TransportHistory $transportHistory): void
    {
        $this->entityManager->persist($transportHistory);
        $this->entityManager->flush();
    }

    public function delete(TransportHistory $transportHistory): void
    {
        $this->entityManager->remove($transportHistory);
        $this->entityManager->flush();
    }

    /**
     * @return array<object>
     */
    public function getTransportHistoryById(int $id): array
    {
        return $this->entityManager
            ->getRepository(TransportHistory::class)
            ->findBy(['id' => $id]);
    }

    /**
     * @return array<object>
     */
    public function getAllTransportHistories(): array
    {
        return $this->entityManager
            ->getRepository(TransportHistory::class)
            ->findAll();
    }

    public function createTransportHistory(Request $request, string $user, string $clientIp): ?Response
    {
        $requestData = (array) $request->request->all()['stock_in_final'];
        $entities = [];
        $actualDateTime = $this->dateTimeService->createDateTime();

        try {
            foreach ($requestData as $key => $data) {
                $data = StockInFinalDto::hydrate((array) $data);

                $entities[] = (new TransportHistory())
                    ->setSuId($data->getStockSuId())
//                    ->setTrNr($this->getLastTransportHistoryNr() + 1)
                    ->setTrPos($key + 1)
                    ->setTrPrio(0)
                    ->setArticleNr($data->getArticleNr())
                    ->setTrQuantity($data->getStockQuantity())
                    ->setStockCoordinate($data->getStockCoordinate())
                    ->setStockNr($data->getStockLn())
                    ->setStockLevel1($data->getStockFb())
                    ->setStockLevel2($data->getStockSp())
                    ->setStockLevel3($data->getStockTf())
                    ->setStockLevel4(1)
//                    ->setFromStockCoordinate((string) $data->getStockCoordinate())
//                    ->setFromStockNr((int) $data->getStockLn())
//                    ->setFromStockLevel1((int) $data->getStockFb())
//                    ->setFromStockLevel2((int) $data->getStockSp())
//                    ->setFromStockLevel3((int) $data->getStockTf())
//                    ->setFromStockLevel4(1)
//                    ->setToStockCoordinate((string) $data->getStockCoordinate())
//                    ->setToStockNr((int) $data->getStockLn())
//                    ->setToStockLevel1((int) $data->getStockFb())
//                    ->setToStockLevel2((int) $data->getStockSp())
//                    ->setToStockLevel3((int) $data->getStockTf())
//                    ->setToStockLevel4(1)
                    ->setTrAccess($actualDateTime)
                    ->setTrState(0)
                    ->setOrderUsername($user)
                    ->setBookingMethod($data->getBookingMethod())
                    ->setDocId(0)
                    ->setCharge($data->getCharge())
                    ->setTrComputerIp($clientIp)
                    ->setLoadingEquipment($data->getLoadingEquipment())
                    ->setTrType(1)
                    ->setCreatedAt($actualDateTime);

                //$this->stockOccupancyService->updateStockOccupancy($data, $actualDateTime);
            }

            foreach ($entities as $entity) {
                $this->entityManager->persist($entity);
            }

            $this->entityManager->flush();
            $this->entityManager->clear();

            return new Response('success');
        } catch (Exception) {
            return null;
        }
    }

    /** @return TransportHistory[] */
    public function getLastStockUnit(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('the')
            ->from(TransportHistory::class, 'the')
            ->setMaxResults(1)
            ->addOrderBy('the.suId', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getLastTransportHistoryNr(): int
    {
        $lastTransportHistoryNr = $this->entityManager
            ->getRepository(TransportHistory::class)
            ->findBy([], ['trNr' => 'DESC'], 1, 0);

        return $lastTransportHistoryNr[0]->getTrNr(); // @phpstan-ignore-line
    }

    public function addTransportHistory(TransportHistory $transportHistory): void
    {
        $transportHistory->setCreatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportHistory);
    }

    public function updateTransportHistory(TransportHistory $transportHistory): void
    {
        $transportHistory->setUpdatedAt(
            $this->dateTimeService->createDateTime()
        );

        $this->save($transportHistory);
    }

    public function deleteTransportHistory(TransportHistory $transportHistory): void
    {
        $this->delete($transportHistory);
    }
}
