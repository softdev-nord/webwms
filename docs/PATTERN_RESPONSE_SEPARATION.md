/**
 * PATTERN: Response-Trennung (T5.2)
 * 
 * DataHandler/Services liefern Daten/Objekte, Controller bauen Response.
 * 
 * FALSCH (aktuell häufig):
 *   public function createTransportRequest(...): ?Response {
 *       try {
 *           // Geschäftslogik
 *           return new Response('success');
 *       } catch (Exception) {
 *           return null;
 *       }
 *   }
 * 
 * RICHTIG (Ziel T5.2):
 *   // In DataHandler:
 *   public function createTransportRequest(...): TransportRequest {
 *       // Geschäftslogik
 *       $this->entityManager->flush();
 *       return $transportRequest;
 *   }
 * 
 *   // In Controller:
 *   public function create(Request $request): JsonResponse {
 *       try {
 *           $tr = $dataHandler->createTransportRequest(...);
 *           return new JsonResponse(['success' => true, 'id' => $tr->getId()]);
 *       } catch (Exception $e) {
 *           return new JsonResponse(['error' => $e->getMessage()], 500);
 *       }
 *   }
 * 
 * STATUS: Dokumentiert als Pattern für zukünftige T5.2-Umsetzung
 * PRIORITÄT: Mittelfristig (nicht-kritisch, aber verbessert Testbarkeit)
 */

