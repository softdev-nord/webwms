<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\User;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Form\User\ChangePasswordType;
use WebWMS\Form\User\Model\ChangePassword;

/**
 * @package:    WebWMS\Tests\Unit\Form\User
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ChangePasswordTypeTest
 *
 * @covers \WebWMS\Form\User\ChangePasswordType
 */
final class ChangePasswordTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::exactly(1))
            ->method('add')
            ->withConsecutive(
                ['oldPassword', PasswordType::class, self::anything()],
                ['newPassword', PasswordType::class, self::anything()],
                ['save', ButtonType::class, self::anything()],
                ['abort', ButtonType::class, self::anything()]
            );

        $type = new ChangePasswordType();
        $type->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with([
                'data_class' => ChangePassword::class,
                'allow_extra_fields' => true,
            ]);

        $type = new ChangePasswordType();
        $type->configureOptions($resolver);
    }
}
