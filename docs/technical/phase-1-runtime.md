# Phase 1: Runtime und Qualitätsbasis

## Ziel

Phase 1 stellt eine reproduzierbare technische Basis für den Neuaufbau von
WebWMS 3.0 bereit. Bestehender Anwendungscode bleibt lauffähig, während neue
Module schrittweise in die modulare Architektur überführt werden.

## Runtime

| Komponente | Version |
| --- | --- |
| PHP | 8.4 oder neuer |
| Symfony | 8.1 |
| Doctrine ORM | 3.7 |
| Doctrine DBAL | 4.4 |
| MariaDB | 10.6 |
| PHPUnit | 11 |
| PHPStan | 2 |

Die Abhängigkeiten sind im `composer.lock` fixiert. Ein separater
Dependency-Lock-Workflow kann das Lockfile in einer vollständigen
PHP-8.4-Umgebung neu erzeugen.

## Symfony-8-Migration

- alte Doctrine-Proxy-Konfiguration entfernt;
- Framework- und WebProfiler-Routen von XML auf PHP umgestellt;
- Serializer-`Groups` von Annotationen auf Attribute migriert;
- Doctrine-DBAL-4-Inkompatibilitäten korrigiert;
- PHPStan-Baseline auf dem neuen Runtime-Stand bereinigt.

## Architekturgrundlage

WebWMS 3.0 wird als modularer Monolith aufgebaut. Die Schichten sind:

- `Domain`: fachliche Regeln ohne Symfony- oder Doctrine-Abhängigkeiten;
- `Application`: Use Cases und Ports;
- `Infrastructure`: DBAL, Symfony Security und externe Adapter;
- `UI`: Controller, API und Oberflächen.

Der gemeinsame `AggregateRoot` speichert Domain Events bis zur Übergabe an
einen späteren Event- beziehungsweise Outbox-Publisher.

## CI-Prüfungen

Die GitHub-Actions-Pipeline prüft:

1. Composer-Installation und Lockfile;
2. PHP-Plattformanforderungen;
3. Symfony-Container;
4. ECS-Code-Style;
5. PHPStan Level 9;
6. Unit-Tests der neuen 3.0-Module.
