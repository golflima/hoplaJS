# Debian 8 / PHP 7.0 / Apache 2.4
FROM debian:jessie

    # Install base packages
RUN     apt-get update && apt-get install -y --fix-missing --no-install-recommends \
            apache2 \
            apt-utils \
            ca-certificates \
            curl \
            dos2unix \
            git \
            ssl-cert \
            sudo \
    # Use Dotdeb repository, to get PHP 7 on Debian 8
    &&  echo "deb http://packages.dotdeb.org jessie all" > /etc/apt/sources.list.d/dotdeb.list \
    &&  curl -sSL https://www.dotdeb.org/dotdeb.gpg | apt-key add - \
    # Use Nodesource, to get Node.js 6x on Debian 8
    &&  curl -sSL https://deb.nodesource.com/setup_6.x | bash - \
    &&  apt-get update \
    # Install PHP + Node.js
    &&  apt-get install -y --fix-missing --no-install-recommends \
            imagemagick \
            graphicsmagick \
            libapache2-mod-php7.0 \
            nodejs \
            php7.0 \
            php7.0-curl \
            php7.0-json \
            php7.0-xdebug \
            php7.0-zip \
    # Remove unnecessary files left after installations
    &&  apt-get clean -y && apt-get autoclean -y && rm -r /var/lib/apt/lists/* \
    # Ensure PHP7 and Node.js are correctly installed
    &&  php -v \
    &&  echo -n "Node.js " && node -v && echo -n "NPM v" && npm -v \
    # Install Composer
    &&  curl -sSL https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer \
    # Install common Node.js tools
    &&  npm install -g bower gulp \
    # Create mountpoint for the web application
    &&  mkdir /var/www/docker \
    &&  chmod -R 777 /var/www/docker \
    # Create a non privileged user
    &&  useradd --create-home --groups sudo --shell /bin/bash docker \
    &&  echo "docker ALL=(ALL) NOPASSWD: ALL" > /etc/sudoers.d/docker

# Set the workdir to mountpoint and the user
USER docker
WORKDIR /var/www/docker/

# Run the install script
CMD [ "setup/docker/docker.start" ]