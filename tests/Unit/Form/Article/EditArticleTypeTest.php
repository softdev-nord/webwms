<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;
use WebWMS\Form\Article\EditArticleType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Article
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        EditArticleTypeTest
 *
 * @covers \WebWMS\Form\Article\EditArticleType
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
final class EditArticleTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['articleId', HiddenType::class, self::anything()],
                ['articleNr', TextType::class, self::anything()],
                ['articleName', TextType::class, self::anything()],
                ['articleCategory', TextType::class, self::anything()],
                ['articleWeight', TextType::class, self::anything()],
                ['articleEan', TextType::class, self::anything()],
                ['articleUnit', TextType::class, self::anything()],
                ['articleDepth', TextType::class, self::anything()],
                ['articleWidth', TextType::class, self::anything()],
                ['articleHeight', TextType::class, self::anything()],
                ['stockOutStrategy', ChoiceType::class, self::anything()],
                ['standardLoadingEquipment', ChoiceType::class, self::anything()],
                ['leQuantity', TextType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new EditArticleType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => Article::class]);

        $type = new EditArticleType();
        $type->configureOptions($resolverMock);
    }
}
