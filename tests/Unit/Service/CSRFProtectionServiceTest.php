<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use WebWMS\Service\CSRFProtectionService;

/**
 * @package:    WebWMS\Tests\Unit\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CSRFProtectionServiceTest
 *
 * @covers \WebWMS\Service\CSRFProtectionService
 */
final class CSRFProtectionServiceTest extends TestCase
{
    private CSRFProtectionService $cSRFProtectionService;

    private RequestStack $requestStack;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->cSRFProtectionService = new CSRFProtectionService($this->requestStack);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->cSRFProtectionService);
        unset($this->requestStack);
    }

    public function testGetCSRFTokenForForm(): void
    {
        $token = $this->cSRFProtectionService->getCSRFTokenForForm();

        self::assertIsString($token);
    }

    public function testValidateCSRFToken(): void
    {
        $token = $this->cSRFProtectionService->getCSRFTokenForForm();
        $request = new Request([], ['_csrf_token' => $token]);

        $result = $this->cSRFProtectionService->validateCSRFToken($request, true, $token);

        self::assertTrue($result);
    }

    public function testValidateCSRFTokenWithInvalidToken(): void
    {
        $invalidToken = 'invalid_token';
        $request = new Request([], ['_csrf_token' => $invalidToken]);

        $result = $this->cSRFProtectionService->validateCSRFToken($request);

        self::assertFalse($result);
    }
}
