#!/bin/bash

# Create storage directory and run fresh migrations with seeding on Heroku

# Heroku app name
HEROKU_APP=nme-app

echo "Starting setup for $HEROKU_APP ..."

# heroku run --app $HEROKU_APP bash -a $HEROKU_APP -- 'mkdir -p storage/app/public && cp -r data/* storage/app/public/'
# echo "Storage directory created and static files copied successfully from data to storage/app/public"

# heroku run --app $HEROKU_APP -- php artisan storage:link --force --no-interaction
# echo "Storage linked!"

heroku run --app $HEROKU_APP -- php artisan migrate:fresh --seed --force --no-interaction
echo "Database refreshed and seeded!"
