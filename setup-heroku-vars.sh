#!/bin/bash

# Replace this with your Heroku app name
HEROKU_APP=nme-app

echo "Setting up Laravel environment variables for $HEROKU_APP ..."

# Core App Settings
heroku config:set APP_NAME="Naija Mineral Exchange" --app $HEROKU_APP
heroku config:set APP_ENV=production --app $HEROKU_APP
heroku config:set APP_KEY=$(php artisan key:generate --show) --app $HEROKU_APP
heroku config:set APP_DEBUG=false --app $HEROKU_APP
heroku config:set APP_URL="https://$HEROKU_APP-096e59437f68.herokuapp.com/" --app $HEROKU_APP
heroku config:set FRONTEND_URL="https://nme-v1.vercel.app" --app $HEROKU_APP

# Database settings
heroku config:set DB_CONNECTION=pgsql --app $HEROKU_APP

# Logging (Heroku best practice)
heroku config:set LOG_CHANNEL=errorlog --app $HEROKU_APP

# Cache, Session & Queue (avoiding file driver on Heroku)
heroku config:set CACHE_DRIVER=database --app $HEROKU_APP
heroku config:set SESSION_DRIVER=database --app $HEROKU_APP
heroku config:set QUEUE_CONNECTION=database --app $HEROKU_APP

# CORS settings
heroku config:set CORS_ALLOWED_ORIGINS=https://nme-v1.vercel.app,https://naija-mineral-exchange.vercel.app,https://nme-frontend-app-afc4f6bdf45f.herokuapp.com,http://localhost:3000 --app $HEROKU_APP

# Mail settings
heroku config:set MAIL_MAILER=mailgun --app $HEROKU_APP
heroku config:set MAIL_MAILER_MAILGUN_DOMAIN=sandbox1db8a4971adb4c30bef44f365a1308eb.mailgun.org --app $HEROKU_APP
heroku config:set MAIL_MAILER_MAILGUN_SECRET=SECRETE_HERE --app $HEROKU_APP
heroku config:set MAIL_MAILER_MAILGUN_ENDPOINT=api.mailgun.net --app $HEROKU_APP
heroku config:set MAIL_FROM_ADDRESS="alhassan88@gmail.com" --app $HEROKU_APP
heroku config:set MAIL_FROM_NAME="${APP_NAME}" --app $HEROKU_APP

echo "All essential Laravel environment variables have been set!"
