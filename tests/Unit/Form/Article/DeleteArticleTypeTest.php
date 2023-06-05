<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Article;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\Article;
use WebWMS\Form\Article\DeleteArticleType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Article
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteArticleTypeTest
 *
 * @covers \WebWMS\Form\Article\DeleteArticleType
 */
final class DeleteArticleTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['articleId', HiddenType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new DeleteArticleType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => Article::class]);

        $type = new DeleteArticleType();
        $type->configureOptions($resolverMock);
    }
}
