FROM php:8.1-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install nodejs
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . /var/www

#COPY .env.example .env

# Install application dependencies
RUN composer install --no-interaction --no-progress --prefer-dist


# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

## Set file permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

#Install npm packages
RUN npm install

#Build assets
RUN npm run build

RUN php artisan optimize:clear

USER $user

# Expose port 9000 for PHP-FPM
EXPOSE 9000
