<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\ArticleEntity;
use WebWMS\Form\Article\DeleteArticleType;

/**
 * @package:    WebWMS\Tests\Unit\Form\ArticleController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        DeleteArticleTypeTest
 */
#[CoversClass(DeleteArticleType::class)]
final class DeleteArticleTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $deleteArticleType = new DeleteArticleType();
        $deleteArticleType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => ArticleEntity::class]);

        $deleteArticleType = new DeleteArticleType();
        $deleteArticleType->configureOptions($resolverMock);
    }
}
