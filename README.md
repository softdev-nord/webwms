![Bitbucket pipelines](https://img.shields.io/bitbucket/pipelines/softdev-nord/webwms/master)

# webWMS
**Das webbasierte Lagerverwaltungssystem**
`https://webwms-dev.softdev-nord.de`

## WebWMS 3.0

Version 3.0 is being rebuilt as a modular monolith on the
`feature/WEBWMS-3.0.0` branch. The target baseline is PHP 8.4 and Symfony 8.1.
Architecture decisions and module boundaries are documented in
[`docs/architecture/`](docs/architecture/README.md).

## Testzugang

Für lokale Testsysteme muss ein eigener Benutzer mit einem individuellen Passwort
angelegt werden. Zugangsdaten dürfen nicht im Repository hinterlegt werden.

## Systemvoraussetzung:
* min. **PHP** `8.2`
* **MySQL**, **MariaDB**
* **Apache**/**Nginx**
* **NodeJs**

## Tech-Stack:
* **Symfony** `7.0`
* **jQuery** `3.6`
* **Twig** `3.8`
* **NodeJs** `v18`
* **API-Platform/Core** `3.2` `Hauptmenu >>> API Dokumentation`
* **Symfony/UX-Chart** `Auswertungen >>> Dashboard`

## CI / CD:
### Bitbucket Pipelines
#### *Folgende Tools sind als Steps in den Pipelines eingebaut:*
- [x] **PHP CS Fixer**
- [x] **PHP Static Analysis**
- [x] **Var Dump Check**
- [x] **Dependency Vulnerability Scan**
- [x] **PHP Mess Detector**
- [x] **Twig Linter**
- [x] **Yaml Linter**
- [x] **PHPUnit**


## QA-Tools:
- [x] **PHP CS Fixer**
    + `friendsofphp/php-cs-fixer`
- [x] **PHP Static Analysis**
    + `phpstan/phpstan`
    + `phpstan/phpstan-doctrine`
    + `phpstan/phpstan-strict-rules`
    + `phpstan/phpstan-symfony`
- [x] **PHP Mess Detector**
    + `phpmd/phpmd`
- [x] **Dependency Vulnerability Scan**
    + `https://api.github.com/repos/fabpot/local-php-security-checker/releases/latest`
* **PHPUnit**
  + `phpunit/phpunit`

## Dev-Tools:
- [x] **Symfony Profiler**
  + `symfony/web-profiler-bundle`
- [x] **Var Dump Check**
  + `php-parallel-lint/php-var-dump-check`
- [x] **Twig Inspector**
  + `oro/twig-inspector`
- [x] **Rector**
  + `rector/rector`

## Lokale Entwicklungsumgebung:
* **PHP** `8.4`
* **Apache** `2.4`
* **MariaDB** `10.6`
* **PhpMyAdmin** `latest`

## Installation:
Derzeit werden die folgenden Plattformen unterstützt:

* Linux

OSX und Windows sind ungetestet

### Anforderungen:
Die folgenden Programme müssen auf Ihrem System vorhanden sein:

* **Docker** siehe https://docs.docker.com/engine/install/
* **Docker Compose** siehe https://docs.docker.com/compose/install/

### Hosts Eintrag hinzufügen:
* Zuordnung von Hostnamen zu den IP-Adressen in der `hosts`.

```shell
172.45.0.2 webwms.local www.webwms.local
172.45.0.4 pma.webwms.local
```

### Git-Repositories klonen/auschecken:
* Klonen Sie dieses Repository auf Ihrem lokalen Computer
* Konfigurieren Sie `.env` nach Bedarf
* Führen Sie den Befehl `docker-compose up -d` aus.

```shell
git clone https://bitbucket.org/softdev-nord/webwms.git
cd webwms/
# Erstellung der .env
cp .env.local .env
# .env nach Bedarf konfigurieren
docker-compose up -d
# Besuchen Sie http://webwms.local
```

---

## Zusätzliches

#### Ein selbst signiertes SSL-Zertifikat erstellen
```shell
# In den Zertifikatsordner wechseln
cd webwms/.docker/config/certs

#######################
# Zertifizierungsstelle
#######################

# Verwenden Sie Ihren eigenen Domainnamen
NAME=webwms.local

# Privaten Schlüssel generieren
openssl genrsa -des3 -out $NAME.rootCA.key 2048
# Root-Zertifikat generieren
openssl req -x509 -new -nodes -key $NAME.rootCA.key -sha256 -days 825 -out $NAME.rootCA.pem

####################################
# CA-signierte Zertifikate erstellen
####################################

# Erzeugen eines privaten Schlüssels
openssl genrsa -out $NAME.key 2048
# Erstellen einer Zertifikatsignierungsanfrage.
openssl req -new -key $NAME.key -out $NAME.csr
# Erstellen einer Konfigurationsdatei für die Erweiterungen.
$NAME.ext cat <<-EOF
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = @alt_names
[alt_names]
DNS.1 = $NAME # Achten Sie darauf, den Domänennamen hier einzuschließen, da der Common Name allein nicht so häufig beachtet wird.
DNS.2 = foo.$NAME # Optional können Sie weitere Domänen hinzufügen (ich habe hier eine Subdomäne hinzugefügt)
EOF
# Erstellen Sie das signierte Zertifikat
openssl x509 -req -in $NAME.csr -CA $NAME.rootCA.pem -CAkey $NAME.rootCA.key -CAcreateserial -out $NAME.crt -days 825 -sha256 -extfile $NAME.ext
```

1. CA-signierte Zertifikate erstellen
2. Signieren Sie Ihr Zertifikat mit Ihrem CA cert+key
3. Importieren Sie `webwms.local.rootCA.pem` als "Autorität" (nicht in "Ihre Zertifikate") in Ihren Chrome-Einstellungen (Einstellungen > Zertifikate verwalten > Autoritäten > Importieren)
4. Verwenden Sie die Dateien `webwms.local.crt` und `webwms.local.key` auf Ihrer Umgebung

##### Mit folgendem Befehl können Sie sicherstellen, dass das Zertifikat korrekt erstellt wurde:

```shell
openssl verify -CAfile webwms.local.rootCA.pem -verify_hostname bar.webwms.local webwms.local.crt
```
