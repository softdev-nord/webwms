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
use WebWMS\Form\Article\AddArticleType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Article
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        AddArticleTypeTest
 *
 * @covers \WebWMS\Form\Article\AddArticleType
 */
final class AddArticleTypeTest extends TestCase
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

        $type = new AddArticleType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => Article::class]);

        $type = new AddArticleType();
        $type->configureOptions($resolver);
    }
}
