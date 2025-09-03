#!/bin/bash

# Replace this with your Heroku app name
HEROKU_APP=nme-app

echo "Setting up Laravel environment variables for $HEROKU_APP ..."

# Core App Settings
heroku config:set APP_NAME="Naija Mineral Exchange" --app $HEROKU_APP
heroku config:set APP_ENV=production --app $HEROKU_APP
heroku config:set APP_KEY=$(php artisan key:generate --show) --app $HEROKU_APP
heroku config:set APP_DEBUG=false --app $HEROKU_APP
heroku config:set APP_URL="https://$HEROKU_APP.herokuapp.com" --app $HEROKU_APP

# Database settings
heroku config:set DB_CONNECTION=pgsql --app $HEROKU_APP

# Logging (Heroku best practice)
heroku config:set LOG_CHANNEL=errorlog --app $HEROKU_APP

# Cache, Session & Queue (avoid file driver on Heroku)
heroku config:set CACHE_DRIVER=database --app $HEROKU_APP
heroku config:set SESSION_DRIVER=database --app $HEROKU_APP
heroku config:set QUEUE_CONNECTION=database --app $HEROKU_APP

# Mail settings
# heroku config:set MAIL_MAILER=smtp --app $HEROKU_APP
# heroku config:set MAIL_HOST=smtp.mailgun.org --app $HEROKU_APP
# heroku config:set MAIL_PORT=587 --app $HEROKU_APP
# heroku config:set MAIL_USERNAME=your@mailgun.user --app $HEROKU_APP
# heroku config:set MAIL_PASSWORD=yourpassword --app $HEROKU_APP
# heroku config:set MAIL_ENCRYPTION=tls --app $HEROKU_APP
# heroku config:set MAIL_FROM_ADDRESS=api@yourdomain.com --app $HEROKU_APP
# heroku config:set MAIL_FROM_NAME="My Laravel API" --app $HEROKU_APP

echo "All essential Laravel environment variables have been set!"
