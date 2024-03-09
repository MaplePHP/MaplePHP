FROM php:8.3-apache

# Install additional PHP modules
RUN docker-php-ext-install pdo_mysql mysqli

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Enable Apache mod_rewrite
RUN a2enmod rewrite headers expires
