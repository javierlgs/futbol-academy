# Imagen base con PHP 8.2 + Apache
FROM php:8.2-apache

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install gd \
    && docker-php-ext-install exif \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Copiar archivos del proyecto
COPY . /var/www/html

# Configurar Apache para servir Laravel desde /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Crear archivo .env dentro del contenedor usando variables de Render
RUN echo "APP_ENV=production" > /var/www/html/.env \
    && echo "APP_KEY=${APP_KEY}" >> /var/www/html/.env \
    && echo "APP_DEBUG=false" >> /var/www/html/.env \
    && echo "DB_CONNECTION=pgsql" >> /var/www/html/.env \
    && echo "DB_HOST=${DB_HOST}" >> /var/www/html/.env \
    && echo "DB_PORT=${DB_PORT}" >> /var/www/html/.env \
    && echo "DB_DATABASE=${DB_DATABASE}" >> /var/www/html/.env \
    && echo "DB_USERNAME=${DB_USERNAME}" >> /var/www/html/.env \
    && echo "DB_PASSWORD=${DB_PASSWORD}" >> /var/www/html/.env

    
# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Dar permisos a storage y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto
EXPOSE 80

# Comando de inicio
CMD ["apache2-foreground"]
