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

sleep 5

docker build -f setup/Dockerfile.base -t base_image .

docker compose -f setup/docker-compose.prod.yml up -d --build

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  composer install --no-dev --optimize-autoloader

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan env:decrypt --env=production --key="${APP_KEY}"

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  cp .env.production .env

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan key:generate --force

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  npm install

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan migrate --force

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan db:seed --force

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan optimize:clear

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan optimize

docker compose -f setup/docker-compose.prod.yml exec -T app \
  service apache2 reload