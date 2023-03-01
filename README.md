![CircleCI](https://img.shields.io/circleci/build/bitbucket/softdev-nord/webwms/master?style=for-the-badge)

# webWMS
**Das webbasierte Lagerverwaltungssystem**
`https://webwms-dev.softdev-nord.de`

**Test User:**
* Benutzername: `testAdmin`
* Passwort: `WebWms2023!#`

## Systemvoraussetzung:
* min. PHP 8.1
* MySQL, MariaDB
* Apache/Nginx
* NodeJs

## Tech-Stack:
* Symfony 6.1
* jQuery
* Twig
* NodeJs
* API-Platform/Core `Hauptmenu >>> API Dokumentation`
* Nelmio/Cors-Bundle
* Symfony/UX-Chart `Dashboard`

## CI / CD:
* CircleCI

## QA-Tools:
* PHP CS Fixer
* PHP Static Analysis
* PHP Mess Detector
* Dependency Vulnerability Scan

## Lokale Entwicklungsumgebung:
* PHP 8.1
* Apache
* MariaDB 10.5
* PhpMyAdmin (latest)

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
* Konfigurieren Sie .env nach Bedarf
* Führen Sie den Befehl `docker-compose up -d` aus.

```shell
git clone https://bitbucket.org/softdev-nord/webwms.git
cd webwms/
# Erstellung der .env
cp .env.template .env
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
3. Importieren Sie "webwms.local.rootCA.pem" als "Autorität" (nicht in "Ihre Zertifikate") in Ihren Chrome-Einstellungen (Einstellungen > Zertifikate verwalten > Autoritäten > Importieren)
4. Verwenden Sie die Dateien `webwms.local.crt` und `webwms.local.key` auf Ihrer Umgebung

##### Mit folgenden Befehl können Sie sicherstellen, dass das Zertifikat korrekt erstellt wurde:

```shell
openssl verify -CAfile webwms.local.rootCA.pem -verify_hostname bar.webwms.local webwms.local.crt
```