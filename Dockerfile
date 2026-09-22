FROM php:8.2-apache

# Instalar librerías del sistema y extensiones de PostgreSQL para PHP
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copiar el código del proyecto al directorio web de Apache
COPY . /var/www/html/

# Exponer el puerto por defecto de Apache
EXPOSE 80