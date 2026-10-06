<?php

declare(strict_types=1);

namespace WebWMS\Warehouse\Product\Presentation\Web;

use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Uid\Uuid;
use WebWMS\Security\V3\TenantPermissionUser;
use WebWMS\Warehouse\Product\Application\ProductMasterDataService;

#[Route('/v3/master-data/products', name: 'v3_product_')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductMasterDataService $products,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('inventory.product.read')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'page' => 'product.page.index',
            'products' => $this->products->products($this->user()->tenantId()),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET'])]
    #[IsGranted('inventory.product.write')]
    public function new(): Response
    {
        return $this->render('product/form.html.twig', [
            'page' => 'product.page.new',
            'product' => null,
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[IsGranted('inventory.product.write')]
    public function create(Request $request): RedirectResponse
    {
        $this->csrf($request, 'v3_product_create');
        $user = $this->user();
        $productId = Uuid::v7()->toRfc4122();
        $this->products->create($productId, $user->tenantId(), $request->request->all(), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', $this->translator->trans('product.flash.created', domain: 'product'));

        return $this->redirectToRoute('v3_product_show', ['productId' => $productId]);
    }

    #[Route('/{productId}', name: 'show', methods: ['GET'])]
    #[IsGranted('inventory.product.read')]
    public function show(string $productId): Response
    {
        $user = $this->user();

        return $this->render('product/show.html.twig', [
            'page' => 'product.page.show',
            'product' => $this->products->product($user->tenantId(), $productId),
            'context' => $this->products->context($user->tenantId(), $productId),
        ]);
    }

    #[Route('/{productId}/edit', name: 'edit', methods: ['GET'])]
    #[IsGranted('inventory.product.write')]
    public function edit(string $productId): Response
    {
        return $this->render('product/form.html.twig', [
            'page' => 'product.page.edit',
            'product' => $this->products->product($this->user()->tenantId(), $productId),
        ]);
    }

    #[Route('/{productId}', name: 'update', methods: ['POST'])]
    #[IsGranted('inventory.product.write')]
    public function update(string $productId, Request $request): RedirectResponse
    {
        $this->csrf($request, 'v3_product_update_' . $productId);
        $user = $this->user();
        $this->products->update($user->tenantId(), $productId, $request->request->all(), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', $this->translator->trans('product.flash.updated', domain: 'product'));

        return $this->redirectToRoute('v3_product_show', ['productId' => $productId]);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }

    private function csrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('The form token is invalid.');
        }
    }
}
