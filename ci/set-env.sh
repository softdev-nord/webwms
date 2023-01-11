#!/bin/bash
# Hilfsskript zur Erstellung einer Datei mit Umgebungsvariablen.
set -Eeuo pipefail
shopt -s inherit_errexit

# Definiere Variablen

###> webWMS ###

if [ "$CIRCLE_BRANCH" == "master" ]
then
  export APP_ENV=prod
  export DATABASE_URL=mysql://webwms:${DATABASE_PASSWORD}@127.0.0.1:3306/webwms?serverVersion=5.7
else
  export APP_ENV=dev
  export DATABASE_URL=mysql://webwms-dev:${DATABASE_PASSWORD}@127.0.0.1:3306/webwms-dev?serverVersion=5.7
fi

export APP_NAME=' | webWMS Das webbasierte Lagerverwaltungssystem'
export APP_VERSION='Enterprise Version'
export APP_VERSION_NUMBER='1.2.0'
export APP_COPYRIGHT='© 2019 Softdev-Nord | Rene Irrgang'
export APP_LIZENZ='Demo Spedition | Demoweg 500 | 21698 Harsefeld'
export APP_SECRET=af2b45ff237087d068938bb0858bddff
export APP_DEBUG=false

export LOCALE=de
export WEB_HOST=https://webwms.softdev-nord.de
###< webWMS ###

### mailer settings ###
export FROM_MAIL=info@softdev-nord.de
export FROM_NAME='webWMS Test'
export RETURN_PATH=info@softdev-nord.de
# Kopie der Mail an die in FROM_MAIL angegebene Adresse geschickt
export MAIL_COPY=true
### mailer settings ###

###> symfony/mailer ###
# e.g. smtp://username:password@yourdomain.tld:port
export MAILER_DSN=null://localhost
###< symfony/mailer ###

# Datei mit Umgebungsvariablen generieren
envsubst < .env.template > .env
