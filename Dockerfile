FROM php:8.2-apache

# Instal·lar dependències del sistema
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_sqlite gd zip

# Habilitar mod_rewrite per al router
RUN a2enmod rewrite

# Configurar el DocumentRoot a /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instal·lar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar fitxers del projecte
WORKDIR /var/www/html
COPY . .

# Permisos per a la base de dades i logs
RUN chown -R www-data:www-data /var/www/html/database /var/www/html/storage

# Instal·lar dependències de Composer (opcional aquí, o fer-ho al container)
# RUN composer install --no-interaction --optimize-autoloader

EXPOSE 80
