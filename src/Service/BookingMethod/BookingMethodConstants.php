<?php

declare(strict_types=1);

namespace WebWMS\Service\BookingMethod;

/**
 * @package:    WebWMS\Service\BookingMethod
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        BookingMethodConstants
 */
class BookingMethodConstants
{
    public const SI101 = 'SI101'; // SI101 Einlagern direkt
    public const SI102 = 'SI102'; // SI102 Zugang aus Bestellung
    public const SI103 = 'SI103'; // SI103 Zugang aus Produktion
    public const SI104 = 'SI104'; // SI104 Rückgabe von Kostenstelle
    public const SI105 = 'SI105'; // SI105 Einlagern in Container
    public const SI106 = 'SI106'; // SI106 WE zur Bestellung
    public const SI107 = 'SI107'; // SI107 Einlagern mit Ladehilfsmittel
    public const SI111 = 'SI111'; // SI111 Einlagern direkt in WE-Zone
    public const SI113 = 'SI113'; // SI113 Einlagern direkt in Kostenstelle
    public const SI114 = 'SI114'; // SI114 Einlagern direkt in WA-Zone

    public const SO151 = 'SO151'; // SO151 Auslagern direkt
    public const SO152 = 'SO152'; // SO152 Auslagern auf Kostenstelle
    public const SO153 = 'SO153'; // SO153 Ausleihen auf Kostenstelle
    public const SO155 = 'SO155'; // SO155 Auslagern aus Container
    public const SO156 = 'SO156'; // SO156 Auslagern aus Kostenstelle
    public const SO157 = 'SO157'; // SO157 Auslagern direkt aus WA-Zone
    public const SO158 = 'SO158'; // SO158 Auftrag auslagern
    public const SO159 = 'SO159'; // SO158 Auftrag auslagern
    public const SO181 = 'SO181'; // SO181 Auftrag auslagern (Auftrag-Liste)
    public const SO182 = 'SO182'; // SO182 Auftrag auslagern mit Kostenstelle (Auftrag-Liste)
    public const SO187 = 'SO187'; // SO187 Auftrag auslagern in WA-Zone
    public const SO188 = 'SO188'; // SO188 Sammelkommissionierung auf Kostenstelle

    public const ST112 = 'ST112'; // ST112 Rückgabe von Kostenstelle
    public const ST183 = 'ST183'; // ST182 Auftrag ausleihe auf Kostenstelle (Auftrag-Liste)
}
