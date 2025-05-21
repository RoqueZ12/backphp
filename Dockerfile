# Usar una imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Habilitar el mod_rewrite de Apache
RUN a2enmod rewrite

# Configuración de la zona horaria (opcional)
RUN echo "date.timezone=America/New_York" > /usr/local/etc/php/conf.d/timezone.ini

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar archivos del proyecto al contenedor
COPY . .

# Instalar dependencias de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Exponer el puerto 80 para acceso web
EXPOSE 80
