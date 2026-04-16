FROM php:8.3-cli

# Install mysqli extension
RUN docker-php-ext-install mysqli pdo_mysql

# Install Git and Composer
RUN apt-get update && apt-get install -y git unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*