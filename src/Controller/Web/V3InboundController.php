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
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Inventory\Application\UnplannedReceiptService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/inbound', name: 'v3_inbound_')]
final class V3InboundController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly UnplannedReceiptService $receipts,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('inbound.receipt.read')]
    public function index(): Response
    {
        return $this->render('v3/inbound/index.html.twig', [
            'page' => 'Wareneingang',
            'receipts' => $this->queries->unplannedReceipts($this->user()->tenantId()),
        ]);
    }

    #[Route('/unplanned/new', name: 'unplanned_new', methods: ['GET', 'POST'])]
    #[IsGranted('inbound.receipt.write')]
    public function create(Request $request): Response
    {
        $user = $this->user();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_inbound_unplanned_create');
            $receipt = $this->receipts->accept(
                $user->tenantId(),
                $this->required($request, 'code'),
                $this->required($request, 'supplier_code'),
                $this->required($request, 'supplier_name'),
                $this->optional($request, 'delivery_note'),
                [[
                    'productId' => $this->required($request, 'product_id'),
                    'locationId' => $this->required($request, 'location_id'),
                    'quantity' => $request->request->getInt('quantity'),
                    'status' => $this->required($request, 'stock_status'),
                    'batchNumber' => $this->optional($request, 'batch_number'),
                    'serialNumber' => $this->optional($request, 'serial_number'),
                ]],
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'Der ungeplante Wareneingang wurde angenommen.');

            return $this->redirectToRoute('v3_inbound_show', ['receiptId' => $receipt->id()->value()]);
        }

        return $this->render('v3/inbound/new.html.twig', [
            'page' => 'Ungeplanter Wareneingang',
            'products' => $this->queries->products($user->tenantId(), 500, null),
            'locations' => $this->queries->receivingLocations($user->tenantId()),
        ]);
    }

    #[Route('/unplanned/{receiptId}', name: 'show', methods: ['GET'])]
    #[IsGranted('inbound.receipt.read')]
    public function show(string $receiptId): Response
    {
        $receipt = $this->queries->unplannedReceipt($this->user()->tenantId(), $receiptId);
        if ($receipt === null) {
            throw $this->createNotFoundException('Der Wareneingang wurde nicht gefunden.');
        }

        return $this->render('v3/inbound/show.html.twig', ['page' => 'Wareneingang', 'receipt' => $receipt]);
    }

    #[Route('/unplanned/{receiptId}/book', name: 'book', methods: ['POST'])]
    #[IsGranted('inbound.receipt.book')]
    public function book(string $receiptId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_inbound_book_' . $receiptId);
        $user = $this->user();
        $this->receipts->book($user->tenantId(), $receiptId, $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'Der Wareneingang wurde atomar in den Bestand gebucht.');

        return $this->redirectToRoute('v3_inbound_show', ['receiptId' => $receiptId]);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
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
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }
}
