# Usa una imagen de PHP con soporte para Composer
FROM php:8.1-apache

# Habilita las extensiones necesarias para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copia los archivos del proyecto al contenedor
COPY . /var/www/html

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala las dependencias de PHP mediante Composer
RUN composer install --no-dev --optimize-autoloader

# Expone el puerto 80 para acceder a la aplicación
EXPOSE 80

# Configura el comando para iniciar Apache
CMD ["apache2-foreground"]
