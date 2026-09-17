<?php

declare(strict_types=1);

namespace WebWMS\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Controller\ArticleController;
use WebWMS\Entity\Article as ArticleEntity;
use WebWMS\Helper\FormHelper\ArticleFormHelper;
use WebWMS\Service\Article\ArticleService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\Validation\ArticleValidationService;

#[CoversClass(ArticleController::class)]
class ArticleControllerTest extends WebTestCase
{
    private ArticleController $controller;

    private MockObject $articleServiceMock;

    private MockObject $requirementsServiceMock;

    private MockObject $articleValidationServiceMock;

    private MockObject $loggingServiceMock;

    private MockObject $articleFormHelperMock;

    private MockObject $userMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->articleServiceMock = $this->createMock(ArticleService::class);
        $this->requirementsServiceMock = $this->createMock(RequirementsService::class);
        $this->articleValidationServiceMock = $this->createMock(ArticleValidationService::class);
        $this->loggingServiceMock = $this->createMock(LoggingService::class);
        $this->articleFormHelperMock = $this->createMock(ArticleFormHelper::class);
        $this->userMock = $this->createMock(UserInterface::class);

        $this->controller = new ArticleController(
            $this->articleServiceMock,
            $this->requirementsServiceMock,
            $this->articleValidationServiceMock,
            $this->loggingServiceMock,
            $this->articleFormHelperMock
        );
    }

    public function testIndex(): void
    {
        $this->userMock->method('getUser')->willReturn($this->userMock);
        $response = $this->controller->index();
        self::assertInstanceOf(Response::class, $response);
    }

    /**
     * @throws Exception
     */
    public function testAddArticle(): void
    {
        $request = new Request();
        $formMock = $this->createMock(FormInterface::class);
        $this->articleFormHelperMock->method('addArticleForm')->willReturn($formMock);
        $formMock->method('handleRequest')->with($request);
        $formMock->method('isSubmitted')->willReturn(true);
        $formMock->method('isValid')->willReturn(true);
        $formMock->method('getData')->willReturn(new ArticleEntity());

        $response = $this->controller->addArticle($request);
        self::assertInstanceOf(Response::class, $response);
    }

    /**
     * @throws Exception
     */
    public function testEditArticle(): void
    {
        $request = new Request();
        $articleId = 1;
        $this->articleServiceMock->method('getArticleById')->with($articleId)->willReturn(new ArticleEntity());
        $formMock = $this->createMock(FormInterface::class);
        $this->articleFormHelperMock->method('editArticleForm')->willReturn($formMock);
        $formMock->method('handleRequest')->with($request);
        $formMock->method('isSubmitted')->willReturn(true);
        $formMock->method('isValid')->willReturn(true);
        $formMock->method('getData')->willReturn(new ArticleEntity());

        $response = $this->controller->editArticle($request, $articleId);
        self::assertInstanceOf(Response::class, $response);
    }

    /**
     * @throws Exception
     */
    public function testDeleteArticle(): void
    {
        $request = new Request();
        $articleId = 1;
        $this->articleServiceMock->method('getArticleById')->with($articleId)->willReturn(new ArticleEntity());
        $formMock = $this->createMock(FormInterface::class);
        $this->articleFormHelperMock->method('deleteArticleForm')->willReturn($formMock);
        $formMock->method('handleRequest')->with($request);
        $formMock->method('isSubmitted')->willReturn(true);
        $formMock->method('isValid')->willReturn(true);
        $formMock->method('getData')->willReturn(new ArticleEntity());

        $response = $this->controller->deleteArticle($request, $articleId);
        self::assertInstanceOf(Response::class, $response);
    }

    public function testGetAllArticles(): void
    {
        $response = $this->controller->getAllArticles();
        self::assertInstanceOf(Response::class, $response);
    }
}
