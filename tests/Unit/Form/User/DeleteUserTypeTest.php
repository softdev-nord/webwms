<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\UserEntity;
use WebWMS\Form\User\DeleteUserType;

/**
 * @package:    WebWMS\Tests\Unit\Form\UserController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        DeleteUserTypeTest
 */
#[CoversClass(DeleteUserType::class)]
final class DeleteUserTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $deleteUserType = new DeleteUserType();
        $deleteUserType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => UserEntity::class]);

        $deleteUserType = new DeleteUserType();
        $deleteUserType->configureOptions($resolverMock);
    }
}
