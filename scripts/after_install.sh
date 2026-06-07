#!/bin/bash
set -e

# Obtener APP_KEY desde SSM
APP_KEY=$(aws ssm get-parameter \
  --name "/backend/app-key" \
  --with-decryption \
  --query "Parameter.Value" \
  --output text \
  --region us-east-1)

cd /var/www/html/public/Infernum-API/API

# Construir imagen base y levantar contenedores
docker build -f setup/Dockerfile.base -t base_image .
docker compose -f setup/docker-compose.prod.yml up -d --build

# Instalar dependencias
docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  composer install --no-dev --optimize-autoloader

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  npm install

# Desencriptar y preparar .env
docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan env:decrypt --env=production --key="${APP_KEY}"

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  cp .env.production .env

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan key:generate

# Limpiar cache antes de migrar
docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan config:clear

docker compose -f setup/docker-compose.prod.yml exec -T -w /var/www/html/public/Infernum-API app \
  php artisan cache:clear

# Esperar a que MySQL esté listo
echo "Esperando a MySQL..."
until docker exec mysql mysqladmin ping -h localhost --silent; do
  echo "MySQL no está listo, esperando..."
  sleep 5
done
echo "MySQL listo"

# Migrar base de datos
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

# Permisos correctos
docker compose -f setup/docker-compose.prod.yml exec -T -u root -w /var/www/html/public/Infernum-API app \
  rm -rf bootstrap/cache/*.php

docker compose -f setup/docker-compose.prod.yml exec -T -u root -w /var/www/html/public/Infernum-API app \
  chown -R www-data:www-data bootstrap/cache storage

# Cachear configuración y rutas
docker compose -f setup/docker-compose.prod.yml exec -T -u www-data -w /var/www/html/public/Infernum-API app \
  php artisan config:clear

docker compose -f setup/docker-compose.prod.yml exec -T -u www-data -w /var/www/html/public/Infernum-API app \
  php artisan config:cache

docker compose -f setup/docker-compose.prod.yml exec -T -u www-data -w /var/www/html/Infernum-API app php artisan livewire:publish --assets --ansi

# Habilitar Docker al inicio y recargar Apache
sudo systemctl enable docker

docker compose -f setup/docker-compose.prod.yml exec -T app \
  service apache2 reload

echo "Deploy completado exitosamente."
