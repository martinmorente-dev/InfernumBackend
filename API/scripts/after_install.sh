#!/bin/bash

# Take the ssmm secret to the apps key for decryption
APP_KEY=$(aws ssm get-parameter \
  --name "/backend/app-key" \
  --with-decryption \
  --query "Parameter.Value" \
  --output text \
  --region us-east-1)

cd ~/home/ubuntu/InfernumAPI

docker compose up -d --build

sleep 5

docker compose exec -T Laravel composer install --no-dev --optimize-autoloader

docker compose exec -T Laravel php artisan env:desencrypt --env=production --key=${APP_KEY}

docker compose exec -T Laravel npm install

docker compose exec -T Laravel php artisan optimize:clear && docker compose exec -T Laravel php artisan optimize

docker compose exec -T Laravel php artisan migrate

docker compose exec -T Laravel php artisan serve --host 0.0.0.0
