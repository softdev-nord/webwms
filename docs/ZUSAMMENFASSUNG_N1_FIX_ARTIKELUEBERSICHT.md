# Zusammenfassung: N+1-Fix Artikeluebersicht (alte Struktur)

## Kontext
- Ausloeser war der Fehler: `Class "WebWMS\Modules\Article\Application\Query\ArticleOverviewReadModelRepositoryInterface" not found`.
- Ziel war, den N+1-Engpass in der Artikeluebersicht zu beheben und gleichzeitig die fehlenden Klassen der alten Modulstruktur wiederherzustellen.

## Umgesetzter N+1-Fix
- In `src/Modules/Article/Infrastructure/Query/DoctrineArticleOverviewReadModelRepository.php` wurde der Datenzugriff auf **native SQL mit `LEFT JOIN` + `GROUP BY`** umgestellt.
- Aggregationen (`SUM`, `COUNT`) passieren direkt in SQL statt pro Artikel in separaten Nachabfragen.
- Ergebnis: konstante Query-Anzahl fuer Listenabruf + Gesamtzaehlung statt N+1-Verhalten.

## Wiederhergestellte alte Struktur (fehlende Klassen)
- `src/Modules/Article/Application/Query/ArticleOverviewReadModelRepositoryInterface.php`
- `src/Modules/Article/Application/Query/GetArticleOverviewQuery.php`
- `src/Modules/Article/Application/Query/GetArticleOverviewQueryHandler.php`
- `src/Modules/Article/Application/Query/View/ArticleOverviewItemView.php`
- `src/Modules/Article/UI/Http/Controller/ArticleOverviewQueryController.php`
- `src/Shared/Dto/Common/PaginationDto.php`
- `src/Shared/Dto/Common/ListResultDto.php`

## Service-Wiring
- In `config/services.yaml` wurde die Modul-Registrierung fuer `WebWMS\Modules\` aktiviert.
- Controller-Services unter `src/Modules/Article/UI/Http/Controller/` sind als `controller.service_arguments` registriert.
- Interface-Binding gesetzt:
  - `WebWMS\Modules\Article\Application\Query\ArticleOverviewReadModelRepositoryInterface`
  - -> `WebWMS\Modules\Article\Infrastructure\Query\DoctrineArticleOverviewReadModelRepository`

## API-Vertrag
- Endpoint: `GET /query/articles/overview`
- Query-Parameter:
  - `page` (default `1`)
  - `limit` (default `50`, max `500`)
  - `search` (optional)
- Auth-Verhalten: ohne Login `401 Unauthorized`.
- Response-Format:

```json
{
  "items": [
    {
      "id": 1,
      "article_nr": "A-100",
      "article_name": "Beispiel",
      "article_description": "...",
      "total_stock": 120.0,
      "incoming_stock": 10.0,
      "location_count": 3
    }
  ],
  "total": 1,
  "page": 1,
  "limit": 50
}
```

## Hinweis
- In der IDE koennen kurzzeitig "Undefined class/namespace"-Hinweise erscheinen, bis Index/Autoload aktualisiert sind.
- Fachlich ist die Struktur fuer den alten Modulpfad wiederhergestellt und der N+1-Fix integriert.

