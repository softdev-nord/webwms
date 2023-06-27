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
    private string $body;

    private string $name;

    private string $contentType;

    private MailAttachment $mailAttachment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->body = 'body';
        $this->name = 'name';
        $this->contentType = 'contentType';
        $this->mailAttachment = new MailAttachment($this->body, $this->name, $this->contentType);
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setId() and getId()
        $id = 1;
        $this->mailAttachment->setId($id);
        self::assertEquals($id, $this->mailAttachment->getId());

        // Test setBody() and getBody()
        $body = $this->body;
        $this->mailAttachment->setBody($body);
        self::assertEquals($body, $this->mailAttachment->getBody());

        // Test setName() and getName()
        $name = $this->name;
        $this->mailAttachment->setName($name);
        self::assertEquals($name, $this->mailAttachment->getName());

        // Test setContentType() and getContentType()
        $contentType = $this->contentType;
        $this->mailAttachment->setContentType($contentType);
        self::assertEquals($contentType, $this->mailAttachment->getContentType());
    }
}
