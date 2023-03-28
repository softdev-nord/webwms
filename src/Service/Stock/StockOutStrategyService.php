<?php

declare(strict_types=1);

namespace WebWMS\Service\Stock;

use WebWMS\Service\DataHandlers\StockOutStrategyDataHandler;

/**
 * @package:    WebWMS\Service\Stock
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockOutStrategyService
 */
class StockOutStrategyService
{
    public function __construct(
        private StockOutStrategyDataHandler $stockOutStrategyDataHandler
    ) {
    }

    /**
     * Weitere Belegungsstrategien die zum Zuordnen von Lagerplätzen genutzt werden:.
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
     * - der Lagerzeit (Kurzzeit oder Langzeitlagerung),
     * - der benötigten Flexibilität und Reaktionszeit
     */

    /**
     * FIFO (First In – First Out).
     *
     * Diese Lagerstrategie ist die Häufigste. Die als Erstes eingelagerten Artikel werden auch als erstes ausgelagert.
     * Oft ist hier auch von einem Durchlauflager die Rede. Was am längsten liegt, wird als erstes genommen,
     * das hält die Lagerzeiten in einem ungefähren Gleichgewicht.
     */
    public function fiFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler->fiFoStrategy();
    }

    /**
     * FEFO (First Expired – First Out).
     *
     * Diese Strategie findet zumeist Anwendung bei Waren mit einem Mindesthaltbarkeitsdatum (MHD) wie Lebensmittel
     * oder Pharmazeutika. Die Ware mit dem frühsten Ablaufdatum wird als erstes ausgelagert.
     */
    public function feFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler->feFoStrategy();
    }

    /**
     * LIFO (Last In – First Out).
     *
     * Bei dieser Lagerstrategie werden die zuletzt eingelagerten Waren zuerst ausgelagert,
     * sodass immer die aktuellste Ware ausgeliefert wird. LIFO findet meist bei Artikeln Anwendung,
     * die sich kaum verändern oder so gut wie keine unterschiedlichen Bestände haben.
     * Bei Einfahrregalen mit nur einer offenen Seite, ist LIFO oft zu finden. Nachteilig ist,
     * dass ältere Artikel länger im Lager verbleiben und so Kosten für einen nicht effektiv genutzten Lagerplatz produzieren.
     */
    public function liFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler->liFoStrategy();
    }

    /**
     * HIFO (Highest In – First Out).
     *
     * Eine eher seltene Lagerstrategie, bei der die Ware mit dem höchsten Wert bzw. Preis
     * oder Beschaffungswert das Lager zuerst verlassen.
     * Vorzugsweise wird diese Strategie bei tagespreis abhängigen oder bei Ware,
     * die durch die Zeit angegriffen wird, eingesetzt.
     */
    public function hiFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler->hiFoStrategy();
    }

    /**
     * LOFO (Lowest In – First Out).
     *
     * Wie HIFO eine ebenfalls selten eingesetzte Strategie.
     * Hier werden Waren mit dem niedrigsten Warenwert zuerst entnommen.
     * Betriebswirtschaftlich macht diese Strategie wenig Sinn,
     * weil die wertvolleren Artikel im Lager bleiben und hohes Kapital binden.
     */
    public function loFoStrategy(): void
    {
        $this->stockOutStrategyDataHandler->loFoStrategy();
    }

    /**
     * Chaotische Lagerhaltung.
     *
     * Das chaotische, oder auch dynamische Lager, bezeichnet eine Lagerform,
     * bei der neue Waren auf einen beliebigen freien Platz gelagert werden.
     * Mithilfe eines chaotischen Lagers wird die Kapazität eines Lagers optimal ausgelastet.
     * Lagerplätze werden nicht freigehalten oder eingeplant, wenn ein Artikel ausgelagert oder ausverkauft ist.
     */
    public function chaoticWarehousingStockOutStrategy(): void
    {
        $this->stockOutStrategyDataHandler->chaoticStorageStockOutStrategy();
    }
}
