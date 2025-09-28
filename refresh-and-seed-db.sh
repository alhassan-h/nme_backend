#!/bin/bash

# Heroku app name
HEROKU_APP=nme-app

echo "Starting setup for $HEROKU_APP ..."

heroku run --app $HEROKU_APP -- php artisan migrate:fresh --seed --force --no-interaction

echo "Database refreshed and seeded!"
