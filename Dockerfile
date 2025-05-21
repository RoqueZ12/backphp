# Usar una imagen base de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Habilitar el mod_rewrite de Apache
RUN a2enmod rewrite

# Configuración de la zona horaria
RUN echo "date.timezone=America/Lima" > /usr/local/etc/php/conf.d/timezone.ini

# Copiar todos los archivos del proyecto al contenedor
COPY . /var/www

# Configurar Apache para usar public/ como DocumentRoot
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|g' /etc/apache2/sites-available/000-default.conf

# Opcional: dar permisos correctos
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependencias si composer.json existe
RUN if [ -f composer.json ]; then composer install; fi

# Exponer el puerto web
EXPOSE 80
