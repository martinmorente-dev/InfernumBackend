#!/bin/bash

MYSQL_ROOT_PASSWORD=$(aws ssm get-parameter --name "/infernum/MYSQL_ROOT_PASSWORD" --with-decryption --query "Parameter.Value" --output text)
MYSQL_USER=$(aws ssm get-parameter --name "/infernum/MYSQL_USER" --with-decryption --query "Parameter.Value" --output text)
MYSQL_PASSWORD=$(aws ssm get-parameter --name "/infernum/MYSQL_PASSWORD" --with-decryption --query "Parameter.Value" --output text)
MYSQL_DATABASE=$(aws ssm get-parameter --name "/infernum/MYSQL_DATABASE" --with-decryption --query "Parameter.Value" --output text)

rm -fr /var/www/html/public/Infernum-API/*

touch /var/www/html/public/Infernum-API/API/setup/.env

cat > /var/www/html/public/Infernum-API/API/setup/.env << EOF
MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
MYSQL_USER=${MYSQL_USER}
MYSQL_PASSWORD=${MYSQL_PASSWORD}
MYSQL_DATABASE=${MYSQL_DATABASE}
EOF