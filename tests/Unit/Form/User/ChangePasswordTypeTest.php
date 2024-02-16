<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Form\User\ChangePasswordType;
use WebWMS\Form\User\Model\ChangePassword;

/**
 * @package:    WebWMS\Tests\Unit\Form\UserController
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ChangePasswordTypeTest
 */
#[CoversClass(ChangePasswordType::class)]
final class ChangePasswordTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $changePasswordType = new ChangePasswordType();
        $changePasswordType->buildForm($builder, []);
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

        $changePasswordType = new ChangePasswordType();
        $changePasswordType->configureOptions($resolver);
    }
}
