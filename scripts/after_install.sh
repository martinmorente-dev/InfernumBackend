#!/bin/bash

# Take the ssmm secret to the apps key for decryption
APP_KEY=$(aws ssm get-parameter \
  --name "/backend/app-key" \
  --with-decryption \
  --query "Parameter.Value" \
  --output text \
  --region us-east-1)

cd ~/var/www/html/public/Infernum-API

docker build -f Dockerfile.base -t base_image

docker compose -f docker-compose.prod up -d --build

docker exec -T Laravel composer install --no-dev --optimize-autoloader

dockere exec -T Laravel php artisan env:descrypt --env=production --key=${APP_KEY}

docker exec -T Laravel npm install

docker exec -T Laravel php artisan optimize:clear && docker  exec -T Laravel php artisan optimize

docker exec -T Laravel php artisan migrate