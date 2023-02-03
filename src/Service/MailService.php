<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use WebWMS\Entity\MailAttachment;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        MailService
 */
class MailService
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        private string $fromMail,
        private string $fromName,
        private string $returnPath,
        private string $mailCopy,
        private MailerInterface $mailer
    ) {
    }

    /**
     * @param  array<string>               $parameter
     * @throws TransportExceptionInterface
     */
    public function sendTemplatedMail(string $to, string $subject, string $template, array $parameter = []): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromMail, $this->fromName))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($parameter)
        ;
        $this->mailer->send($email);
    }

    /**
     * @param  object[]                    $attachments
     * @throws TransportExceptionInterface
     */
    public function sendHTMLMail(string $to, string $subject, string $body, array $attachments = []): void
    {
        $email = (new Email())
            ->from(new Address($this->fromMail, $this->fromName))
            ->to(new Address($to))
            ->subject($subject)
            ->html($body)
        ;

        if ($this->returnPath != $this->fromMail) {
            $email->replyTo(new Address($this->returnPath));
        }

        if ('true' == $this->mailCopy) {
            $email->bcc(new Address($this->fromMail));
        }

//        /* @var $attachment MailAttachment */
//        foreach ($attachments as $attachment) {
//            $email->attach(
//                $attachment->getBody(),
//                $attachment->getName(),
//                $attachment->getContentType()
//            );
//        }
        $this->mailer->send($email);
    }
}
