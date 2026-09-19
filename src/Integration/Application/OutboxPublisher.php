<?php

declare(strict_types=1);

namespace WebWMS\Integration\Application;

use DateTimeImmutable;
use WebWMS\Integration\Domain\OutboxRepository;
use WebWMS\Integration\Domain\OutboxTransport;

final readonly class OutboxPublisher
{
    public function __construct(
        private OutboxRepository $outbox,
        private OutboxTransport $transport,
        private int $maximumAttempts = 5,
        private int $baseDelaySeconds = 30,
        private int $maximumDelaySeconds = 3600,
        private int $leaseSeconds = 300
    ) {
        if ($maximumAttempts < 1 || $baseDelaySeconds < 1 || $maximumDelaySeconds < $baseDelaySeconds || $leaseSeconds < 1) {
            throw new \InvalidArgumentException('The outbox publisher retry configuration is invalid.');
        }
    }

    public function publishDue(int $limit, DateTimeImmutable $now): OutboxPublishReport
    {
        if ($limit < 1 || $limit > 1000) {
            throw new \InvalidArgumentException('The publish limit must be between 1 and 1000.');
        }

        $messages = $this->outbox->claimDue($limit, $now, $now->modify(sprintf('-%d seconds', $this->leaseSeconds)));
        $published = $retryScheduled = $deadLettered = 0;

        foreach ($messages as $message) {
            try {
                $this->transport->publish($message);
                $this->outbox->markPublished($message, $now);
                ++$published;
            } catch (\Throwable $exception) {
                $deadLetter = $message->attemptNumber >= $this->maximumAttempts;
                $nextAttemptAt = $deadLetter ? null : $now->modify(sprintf(
                    '+%d seconds',
                    min($this->maximumDelaySeconds, $this->baseDelaySeconds * (2 ** ($message->attemptNumber - 1))),
                ));
                $this->outbox->markFailed($message, mb_substr($exception->getMessage(), 0, 1000), $now, $nextAttemptAt);
                if ($deadLetter) {
                    ++$deadLettered;
                } else {
                    ++$retryScheduled;
                }
            }
        }

        return new OutboxPublishReport(count($messages), $published, $retryScheduled, $deadLettered);
    }
}
