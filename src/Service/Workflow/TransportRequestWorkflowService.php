<?php

declare(strict_types=1);

namespace WebWMS\Service\Workflow;

use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\WorkflowInterface;
use WebWMS\Entity\TransportRequest;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Workflow',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'TransportRequestWorkflowService'
)]
readonly class TransportRequestWorkflowService
{
    public function __construct(
        #[Target('transport_request')]
        private WorkflowInterface $transportRequestWorkflow,
    ) {
    }

    /**
     * Startet einen Transport (open -> in_progress)
     */
    public function start(TransportRequest $transportRequest): bool
    {
        if ($this->transportRequestWorkflow->can($transportRequest, 'start')) {
            $this->transportRequestWorkflow->apply($transportRequest, 'start');
            return true;
        }
        return false;
    }

    /**
     * Beendet einen Transport (in_progress -> done)
     */
    public function complete(TransportRequest $transportRequest): bool
    {
        if ($this->transportRequestWorkflow->can($transportRequest, 'complete')) {
            $this->transportRequestWorkflow->apply($transportRequest, 'complete');
            return true;
        }
        return false;
    }

    /**
     * Storniert einen Transport (open/in_progress -> cancelled)
     */
    public function cancel(TransportRequest $transportRequest): bool
    {
        if ($this->transportRequestWorkflow->can($transportRequest, 'cancel')) {
            $this->transportRequestWorkflow->apply($transportRequest, 'cancel');
            return true;
        }
        return false;
    }

    /**
     * Archiviert einen Transport (done/cancelled -> archived)
     */
    public function archive(TransportRequest $transportRequest): bool
    {
        if ($this->transportRequestWorkflow->can($transportRequest, 'archive')) {
            $this->transportRequestWorkflow->apply($transportRequest, 'archive');
            return true;
        }
        return false;
    }

    /**
     * Wiedereröffnet einen Transport (cancelled -> open)
     */
    public function reopen(TransportRequest $transportRequest): bool
    {
        if ($this->transportRequestWorkflow->can($transportRequest, 'reopen')) {
            $this->transportRequestWorkflow->apply($transportRequest, 'reopen');
            return true;
        }
        return false;
    }

    /**
     * Gibt alle erlaubten Übergänge für einen Transport zurück
     *
     * @return string[]
     */
    public function getEnabledTransitions(TransportRequest $transportRequest): array
    {
        $enabledTransitions = [];
        foreach ($this->transportRequestWorkflow->getDefinition()->getTransitions() as $transition) {
            if ($this->transportRequestWorkflow->can($transportRequest, $transition->getName())) {
                $enabledTransitions[] = $transition->getName();
            }
        }
        return $enabledTransitions;
    }

    /**
     * Gibt den aktuellen Status eines Transports zurück
     */
    public function getCurrentMarking(TransportRequest $transportRequest): string
    {
        return $transportRequest->getTrState();
    }
}
