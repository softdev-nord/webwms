<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WebWMS\Entity\MailAttachment;

/**
 * @package:    WebWMS\Tests\Unit\Entity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        MailAttachmentTest
 *
 * @covers \WebWMS\Entity\MailAttachment
 */
final class MailAttachmentTest extends TestCase
{
    private MailAttachment $mailAttachment;

    private string $body;

    private string $name;

    private string $contentType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->body = 'body';
        $this->name = 'name';
        $this->contentType = 'contentType';
        $this->mailAttachment = new MailAttachment($this->body, $this->name, $this->contentType);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->mailAttachment);
        unset($this->body);
        unset($this->name);
        unset($this->contentType);
    }

    public function testGetBody(): void
    {
        $expected = 'body';
        $property = (new \ReflectionClass(MailAttachment::class))
            ->getProperty('body');
        $property->setValue($this->mailAttachment, $expected);
        self::assertSame($expected, $this->mailAttachment->getBody());
    }

    public function testSetBody(): void
    {
        $expected = 'body';
        $property = (new \ReflectionClass(MailAttachment::class))
            ->getProperty('body');
        $this->mailAttachment->setBody($expected);
        self::assertSame($expected, $property->getValue($this->mailAttachment));
    }

    public function testGetName(): void
    {
        $expected = 'name';
        $property = (new \ReflectionClass(MailAttachment::class))
            ->getProperty('name');
        $property->setValue($this->mailAttachment, $expected);
        self::assertSame($expected, $this->mailAttachment->getName());
    }

    public function testSetName(): void
    {
        $expected = 'name';
        $property = (new \ReflectionClass(MailAttachment::class))
            ->getProperty('name');
        $this->mailAttachment->setName($expected);
        self::assertSame($expected, $property->getValue($this->mailAttachment));
    }

    public function testGetContentType(): void
    {
        $expected = 'contentType';
        $property = (new \ReflectionClass(MailAttachment::class))
            ->getProperty('contentType');
        $property->setValue($this->mailAttachment, $expected);
        self::assertSame($expected, $this->mailAttachment->getContentType());
    }

    public function testSetContentType(): void
    {
        $expected = 'contentType';
        $property = (new \ReflectionClass(MailAttachment::class))
            ->getProperty('contentType');
        $this->mailAttachment->setContentType($expected);
        self::assertSame($expected, $property->getValue($this->mailAttachment));
    }
}
