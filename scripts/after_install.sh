#!/bin/bash

set -e

# Take the ssmm secret to the apps key for decryption
APP_KEY=$(aws ssm get-parameter \
  --name "/backend/app-key" \
  --with-decryption \
  --query "Parameter.Value" \
  --output text \
  --region us-east-1)

cd /var/www/html/public/Infernum-API/API

docker build -f setup/Dockerfile.base -t base_image .

docker compose -f setup/docker-compose.prod.yml up -d --build

docker compose -f setup/docker-compose.prod.yml exec -T Laravel composer install --no-dev --optimize-autoloader

docker compose -f setup/docker-compose.prod.yml exec -T Laravel php artisan env:decrypt --env=production --key="${APP_KEY}"

docker compose -f setup/docker-compose.prod.yml exec -T Laravel npm install

docker compose -f setup/docker-compose.prod.yml exec -T Laravel php artisan optimize:clear

docker compose -f setup/docker-compose.prod.yml exec -T Laravel php artisan optimize

docker compose -f setup/docker-compose.prod.yml exec -T Laravel php artisan migrate

docker compose -f setup/docker-compose.prod.yml exec -T Laravel service apache2 reload