#!/bin/bash
set -e

# Take the ssm secret to the apps key for decryption
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

# Esperar a que MySQL esté listo
echo "Esperando a MySQL..."
until docker exec mysql mysqladmin ping -h localhost --silent; do
  echo "MySQL no está listo, esperando..."
  sleep 5
done
echo "MySQL listo"

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan migrate --force

# Seedear solo si no hay usuarios
USER_COUNT=$(docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tr -d '[:space:]')

if [ "$USER_COUNT" -eq "0" ] 2>/dev/null; then
  echo "Base de datos vacía, ejecutando seeders..."
  docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
    php artisan db:seed --force
else
  echo "Ya hay datos, omitiendo seeders"
fi

sudo systemctl enable docker

# Limpiar cache viejo y regenerar con la APP_KEY correcta
docker compose -f setup/docker-compose.prod.yml exec -T -u root -w /var/www/html/public/Infernum-API app \
  rm -rf bootstrap/cache/*.php

docker compose -f setup/docker-compose.prod.yml exec -T -u root -w /var/www/html/public/Infernum-API app \
  chown -R www-data:www-data bootstrap/cache storage

docker compose -f setup/docker-compose.prod.yml exec -T -u www-data -w /var/www/html/public/Infernum-API app \
  php artisan config:cache

docker compose -f setup/docker-compose.prod.yml exec -T -u www-data -w /var/www/html/public/Infernum-API app \
  php artisan route:cache

docker compose -f setup/docker-compose.prod.yml exec -T -u www-data -w /var/www/html/public/Infernum-API app \
  php artisan config:clear

docker compose -f setup/docker-compose.prod.yml exec -T app \
  service apache2 reload