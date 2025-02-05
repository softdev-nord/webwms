<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;
use WebWMS\Form\Article\DeleteArticleType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Article',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'DeleteArticleTypeTest'
)]
#[CoversClass(DeleteArticleType::class)]
final class DeleteArticleTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $form = $this->factory->create(DeleteArticleType::class);

        $formData = [
            'articleId' => '123',
        ];

        $form->submit($formData);

        self::assertTrue($form->isSynchronized());
        self::assertSame('123', $form->get('articleId')->getData());
    }

    public function testFormView(): void
    {
        $form = $this->factory->create(DeleteArticleType::class);
        $formView = $form->createView();

        self::assertArrayHasKey('articleId', $formView->children);
        self::assertArrayHasKey('delete', $formView->children);
        self::assertArrayHasKey('abort', $formView->children);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => Article::class]);

        $deleteArticleType = new DeleteArticleType();
        $deleteArticleType->configureOptions($resolverMock);
    }

    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([], []),
        ];
    }
}
