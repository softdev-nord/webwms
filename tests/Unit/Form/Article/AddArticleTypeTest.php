<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\ArticleEntity;
use WebWMS\Form\Article\AddArticleType;

/**
 * @package:    WebWMS\Tests\Unit\Form\ArticleController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        AddArticleTypeTest
 */
#[CoversClass(AddArticleType::class)]
final class AddArticleTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $addArticleType = new AddArticleType();
        $addArticleType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => ArticleEntity::class]);

        $addArticleType = new AddArticleType();
        $addArticleType->configureOptions($resolver);
    }
}
