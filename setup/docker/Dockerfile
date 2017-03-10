# Debian 8 / PHP 7.0 / Apache 2
FROM debian:jessie

    # Install base packages
RUN     apt-get update && apt-get install -y --fix-missing --no-install-recommends \
            apache2 \
            apt-utils \
            ca-certificates \
            curl \
            git \
            ssl-cert \
    # Use Dotdeb repository, to get PHP 7 on Debian 8
    &&  echo "deb http://packages.dotdeb.org jessie all" > /etc/apt/sources.list.d/dotdeb.list \
    &&  curl -sSL https://www.dotdeb.org/dotdeb.gpg | apt-key add - \
    &&  apt-get update \
    # Install PHP
    &&  apt-get install -y --fix-missing --no-install-recommends \
            libapache2-mod-php7.0 \
            php7.0 \
            php7.0-curl \
            php7.0-json \
            php7.0-xdebug \
            php7.0-zip \
    # Remove unessary files left after installations
    &&  apt-get clean -y && apt-get autoclean -y && rm -r /var/lib/apt/lists/* \
    # Ensure PHP7 is correctly installed
    &&  php -v \
    # Install Composer
    &&  curl -sSL https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer \
    # Create moutpoint for the web application
    &&  mkdir /var/www/docker

# Run the install script
CMD [ "/var/www/docker/setup/docker/docker.start" ]