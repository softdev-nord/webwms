<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;
use WebWMS\Form\Article\EditArticleType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Article',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'EditArticleTypeTest'
)]
#[CoversClass(EditArticleType::class)]
final class EditArticleTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'articleId' => '123',
            'articleNr' => 'A001',
            'articleName' => 'Testartikel',
            'articleCategory' => 'Kategorie A',
            'articleWeight' => 1.0,
            'articleEan' => '1234567890123',
            'articleUnit' => 'Stück',
            'articleDepth' => '10',
            'articleWidth' => '20',
            'articleHeight' => '30',
            'stockOutStrategy' => 'FIFO',
            'standardLoadingEquipment' => 'KARTON',
            'leQuantity' => '100',
        ];

        $form = $this->factory->create(EditArticleType::class);
        $form->submit($formData);

        self::assertTrue($form->isSynchronized(), 'Das Formular sollte synchronisiert sein.');
        self::assertTrue($form->isValid(), 'Das Formular sollte gültig sein.');
        self::assertInstanceOf(Article::class, $form->getData());

        $article = $form->getData();
        self::assertSame($formData['articleNr'], $article->getArticleNr());
        self::assertSame($formData['articleName'], $article->getArticleName());
        self::assertSame($formData['articleCategory'], $article->getArticleCategory());
        self::assertSame($formData['articleWeight'], $article->getArticleWeight());
        self::assertSame($formData['articleEan'], $article->getArticleEan());
        self::assertSame($formData['articleUnit'], $article->getArticleUnit());
        self::assertSame($formData['articleDepth'], $article->getArticleDepth());
        self::assertSame($formData['articleWidth'], $article->getArticleWidth());
        self::assertSame($formData['articleHeight'], $article->getArticleHeight());
        self::assertSame($formData['stockOutStrategy'], $article->getStockOutStrategy());
        self::assertSame($formData['standardLoadingEquipment'], $article->getStandardLoadingEquipment());
        self::assertSame($formData['leQuantity'], $article->getLeQuantity());
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => Article::class]);

        $editArticleType = new EditArticleType();
        $editArticleType->configureOptions($resolverMock);
    }
}
