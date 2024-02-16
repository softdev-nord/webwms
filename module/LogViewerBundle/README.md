# LogViewerBundle

## Konfiguration

```php
// config/modules.php
return [
    ...
    WebWMS\Bundles\LogViewerBundle\LogViewerBundle::class => ['all' => true],
];
```

```yaml
# config/routes/web_wms_log_viewer.yaml
web_wms_log_viewer:
    resource: "@LogViewerBundle/Resources/config/routing.yaml"
```

#### Install Bundle assets
```shell
php bin/console assets:install public
```

## Benutzung

Der Protokoll-Viewer ist für Benutzer mit der Berechtigung `ROLE_SUPER_ADMIN` unter `/logs` verfügbar.
