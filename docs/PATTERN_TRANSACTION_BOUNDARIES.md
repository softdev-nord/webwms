/**
 * PATTERN: Transaktionsgrenzen (T5.3)
 * 
 * Mehrschritt-Operationen müssen atomar sein.
 * 
 * IMPLEMENTIERT:
 * - TransportRequestDataHandler::createTransportRequest()
 *   - try/catch mit entityManager->rollback()
 *   - Mehrere persist + 1x flush
 *   - Exception wirft und propagiert
 * 
 * - InventoryService::completeInventory()
 *   - Validiert Voraussetzungen
 *   - Atomar DB-Update
 * 
 * BEST PRACTICES:
 * 1. Validiere ALLES vor persist/flush
 * 2. Mehrere Entities in einer Transaktion
 * 3. Try/catch mit explizitem rollback
 * 4. Werfe aussagekräftige Exceptions
 * 5. Controller handhabt Exceptions mit HTTP-Codes
 * 
 * STATUS: Implementiert in Schreib-Operationen
 * VERBESSERUNG: Symfony Dbal TransactionManager für explizitere Kontrolle
 */

