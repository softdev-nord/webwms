<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\UserEntity;
use WebWMS\Form\User\AddUserType;

/**
 * @package:    WebWMS\Tests\Unit\Form\UserController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        AddUserTypeTest
 */
#[CoversClass(AddUserType::class)]
final class AddUserTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $addUserType = new AddUserType();
        $addUserType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolverMock = $this->createMock(OptionsResolver::class);
        $resolverMock
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => UserEntity::class]);

        $addUserType = new AddUserType();
        $addUserType->configureOptions($resolverMock);
    }
}
