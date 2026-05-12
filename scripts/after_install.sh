#!/bin/bash

# Take the ssmm secret to the apps key for decryption
APP_KEY=$(aws ssm get-parameter \
  --name "/backend/app-key" \
  --with-decryption \
  --query "Parameter.Value" \
  --output text \
  --region us-east-1)

cd /var/www/html/public/Infernum-API/API

docker build -f setup/Dockerfile.base -t base_image .

docker compose -f setup/docker-compose.prod up -d --build

docker compose exec -T Laravel composer install --no-dev --optimize-autoloader

dockere compose exec -T Laravel php artisan env:descrypt --env=production --key=${APP_KEY}

docker compose exec -T Laravel npm install

docker compose exec -T Laravel php artisan optimize:clear && docker compose exec -T Laravel php artisan optimize

docker compose exec -T Laravel php artisan migrate