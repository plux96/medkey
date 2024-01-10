FROM php:7.3-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpq-dev \
    libc-client-dev \
    libkrb5-dev \
    libicu-dev \
    zlib1g-dev \
    git \
    nano \
    graphviz \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pgsql pdo_pgsql zip bcmath intl \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install imap \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

# Setup SSH
RUN mkdir -p ~/.ssh && ssh-keyscan github.com >> ~/.ssh/known_hosts


# Apache configuration
RUN rm -rf /etc/apache2/sites-enabled/000-default.conf
COPY docker/backend/medkey.conf /etc/apache2/sites-available/
RUN a2enmod rewrite && a2ensite medkey.conf

# Install Composer and Codeception
RUN curl -sS https://getcomposer.org/installer | php -- --version=1.10.22 --install-dir=/usr/local/bin --filename=composer \
    && curl -LsS http://codeception.com/codecept.phar -o /usr/local/bin/codecept \
    && chmod a+x /usr/local/bin/codecept

# Node.js and NPM installation
RUN curl -sL https://deb.nodesource.com/setup_18.x -o nodesource_setup.sh \
    && bash nodesource_setup.sh \
    && apt-get install -y nodejs \
    && npm install npm@latest -g

# Set Node.js to use the legacy provider for OpenSSL
ENV NODE_OPTIONS=--openssl-legacy-provider

WORKDIR /var/www/medkey

# Uncomment below lines if these operations are needed
# COPY . /var/www/medkey
# COPY .env.prod .env
# RUN composer install
# WORKDIR /var/www/medkey/frontend
# RUN npm install
# RUN npm run build-prod
# RUN php bin migrate --interactive=0
# RUN php bin seed
