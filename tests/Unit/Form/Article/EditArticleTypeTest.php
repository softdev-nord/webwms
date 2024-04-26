<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\ArticleEntity;
use WebWMS\Form\Article\EditArticleType;

/**
 * @package:    WebWMS\Tests\Unit\Form\ArticleController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        EditArticleTypeTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
#[CoversClass(EditArticleType::class)]
final class EditArticleTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $editArticleType = new EditArticleType();
        $editArticleType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => ArticleEntity::class]);

        $editArticleType = new EditArticleType();
        $editArticleType->configureOptions($resolverMock);
    }
}
