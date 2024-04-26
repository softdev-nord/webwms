<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use WebWMS\Service\CSRFProtectionService;

/**
 * @package:    WebWMS\Tests\Unit\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        CSRFProtectionServiceTest
 */
#[CoversClass(CSRFProtectionService::class)]
final class CSRFProtectionServiceTest extends TestCase
{
    private CSRFProtectionService $csrfProtectionService;

    private MockObject $mockObject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockObject = $this->createMock(RequestStack::class);
        $this->csrfProtectionService = new CSRFProtectionService($this->mockObject);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->csrfProtectionService);
        unset($this->mockObject);
    }

    public function testGetCSRFTokenForForm(): void
    {
        $token = $this->csrfProtectionService->getCSRFTokenForForm();

        self::assertIsString($token);
    }

    public function testValidateCSRFToken(): void
    {
        $token = $this->csrfProtectionService->getCSRFTokenForForm();
        $request = new Request([], ['_csrf_token' => $token]);

        $result = $this->csrfProtectionService->validateCSRFToken($request, true, $token);

        self::assertTrue($result);
    }

    public function testValidateCSRFTokenWithInvalidToken(): void
    {
        $invalidToken = 'invalid_token';
        $request = new Request([], ['_csrf_token' => $invalidToken]);

        $result = $this->csrfProtectionService->validateCSRFToken($request);

        self::assertFalse($result);
    }
}
