<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;
use WebWMS\Form\Article\AddArticleType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\Article',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'AddArticleTypeTest',
    covers: AddArticleType::class
)]
final class AddArticleTypeTest extends TypeTestCase
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

        $form = $this->factory->create(AddArticleType::class);
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

    /**
     * @throws Exception
     */
    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects($this->once())
            ->method('setDefaults')
            ->with(['data_class' => Article::class]);

        $addArticleType = new AddArticleType();
        $addArticleType->configureOptions($resolver);
    }

    protected function getExtensions(): array
    {
        // Falls benutzerdefinierte Form-Typen oder Data Transformers erforderlich sind,
        // können sie hier als PreloadedExtension hinzugefügt werden.
        return [
            new PreloadedExtension([], []),
        ];
    }
}
