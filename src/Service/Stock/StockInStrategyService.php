<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Stock',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockInStrategyService'
)]
class StockInStrategyService
{
    /*
     * Weitere Belegungsstrategien die zum Zuordnen von Lagerplätzen genutzt werden:
     *
     * - Schnellläuferkonzentration bei statischer und dynamischer Bereitstellung
     * - Feste Lagerplatzordnung
     * - Freie Lagerplatzordnung
     * - Zonenweise feste Lagerordnung
     * - Gleichverteilungsstrategie
     * - Platzanpassung
     * - Artikelreine und chargenreine Platzbelegung
     * - Artikelgemischte Platzbelegung
     * - Minimieren von angebrochenen Lagerplätzen
     *
     * Die Wahl der richtigen Strategie hängt von vielen Faktoren ab, wie z.B. von:
     *
     * - der Art der Ware,
     * - der Größe des Lagers,
     * - der Lagerzeit (Kurzzeit oder Langzeitlager),
     * - der benötigten Flexibilität und Reaktionszeit
     */
}
