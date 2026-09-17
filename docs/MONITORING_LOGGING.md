# Monitoring & Structured Logging (T6.3)

## ProcessEventLogger Integration

### Beispiel: TransportRequest Workflow

```php
namespace WebWMS\Controller;

use WebWMS\Service\Logging\ProcessEventLogger;

class TransportRequestWorkflowController extends AbstractController
{
    public function __construct(
        private readonly ProcessEventLogger $eventLogger,
        // ... andere Abhängigkeiten
    ) {
    }

    #[Route('/transport_request/{id}/start', name: 'transport_request_start', methods: ['POST'])]
    public function startTransportRequest(int $id, Request $request): JsonResponse
    {
        $username = $this->getUser()->getUserIdentifier();
        $clientIp = $request->getClientIp();

        try {
            $transportRequest = $this->transportRequestRepository->find($id);
            if (!$transportRequest) {
                $this->eventLogger->error(
                    processCode: 'TA-WORKFLOW',
                    action: 'start',
                    username: $username,
                    clientIp: $clientIp,
                    message: "Transport request {$id} not found",
                    context: ['ta_id' => $id]
                );
                return new JsonResponse(['error' => 'Not found'], 404);
            }

            if ($this->workflowService->start($transportRequest)) {
                $this->transportRequestRepository->save($transportRequest);
                
                // Log erfolgreiche Transition
                $this->eventLogger->success(
                    processCode: 'TA-WORKFLOW',
                    action: 'start',
                    username: $username,
                    clientIp: $clientIp,
                    message: "Transport request {$id} started successfully",
                    context: [
                        'ta_id' => $id,
                        'ta_nr' => $transportRequest->getTrNr(),
                        'old_state' => 'open',
                        'new_state' => 'in_progress',
                    ],
                    metrics: ['duration_ms' => 123]
                );

                return new JsonResponse([
                    'success' => true,
                    'message' => 'Transport request started',
                    'state' => $transportRequest->getTrState(),
                ]);
            }

            return new JsonResponse(['error' => 'Cannot start'], 422);
        } catch (\Exception $e) {
            $this->eventLogger->error(
                processCode: 'TA-WORKFLOW',
                action: 'start',
                username: $username,
                clientIp: $clientIp,
                message: "Error starting transport request: {$e->getMessage()}",
                exception: $e,
                context: ['ta_id' => $id]
            );
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
```

## Log-Format (strukturiert)

```json
{
  "timestamp": "2026-09-02T12:34:56Z",
  "log_id": "LOG-67a8b9c1d2e3f.12345",
  "process_code": "TA-WORKFLOW",
  "action": "start",
  "username": "user@example.com",
  "client_ip": "192.168.1.1",
  "result": "success",
  "message": "Transport request 123 started successfully",
  "context": {
    "ta_id": 123,
    "ta_nr": "TA-0042",
    "old_state": "open",
    "new_state": "in_progress"
  },
  "metrics": {
    "duration_ms": 123
  }
}
```

## Verwendung in Prozessen

| Prozess | Code | Actions |
|---------|------|---------|
| Stock-In | SI101-110 | receive, validate, stash |
| Stock-Out | SO101-110 | pick, verify, dispatch |
| Workflow | TA-WF | start, complete, cancel, archive |
| Inventur | INV | start, count, complete |

## Status

- ✅ ProcessLogEntry definiert
- ✅ ProcessEventLogger mit Helpers
- ✅ Integration-Pattern dokumentiert
- ⏳ Deployment zu ELK/Splunk (zukünftig)

