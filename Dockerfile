FROM php:7.4-apache
RUN docker-php-ext-install mysqli pdo_mysql && a2enmod rewrite

# Install PHPUnit to $PATH
RUN curl -o /usr/local/bin/phpunit https://phar.phpunit.de/phpunit-6.5.14.phar \
 && chmod +x /usr/local/bin/phpunit

