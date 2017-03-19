# ![](../web/assets/images/favicon_32.png) hoplaJS - Installation Manual



## With Docker

*hoplaJS* is shipped with everything needed to use it with [Docker](https://www.docker.com/get-docker).
It was tested on both Windows and Linux, and should works on OS X too.

1. `git clone https://github.com/golflima/hoplaJS.git`
2. `cd hoplaJS`
3. `docker-compose build`
   * For Windows users only, run `docker-compose run web bash -c 'dos2unix setup/docker/*'`
4. `docker-compose up -d`

*hoplaJS* will be accessible from [HTTP](http://localhost:8080) or [HTTPS](https://localhost:8443) when the docker is running.

* To stop the docker, run `docker-compose down`
* To reuse the docker again, run `docker-compose up -d`

Note: This docker provides everything needed for development, and should not be used for production.



## Manual installation

To install manually *hoplaJS*, you *will* need to install before:

* [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git)
* [PHP](http://php.net/manual/en/install.php), v7.0+
* [Composer](https://getcomposer.org/download/)

If you want to run *hoplaJS* on production, you should also install:

* [Apache](https://httpd.apache.org/download.cgi) or [nginx](https://nginx.org/en/download.html)
  * An .htaccess file is provided for Apache in `setup/apache/.htaccess`, you should copy it into `web` if you use Apache
  * For other web servers, instructions to run *hoplaJS* on them can be found on [Silex website](http://silex.sensiolabs.org/doc/2.0/web_servers.html)

If you want to contribute or debug *hoplaJS*, you should also install:

* [Node.js & npm](https://docs.npmjs.com/getting-started/installing-node)
* And run: `npm install`
* [GraphicsMagick or ImageMagick](https://www.npmjs.com/package/gulp-image-resize#install)

Then, run these commands:

1. `git clone https://github.com/golflima/hoplaJS.git`
2. `cd hoplaJS`
3. `composer install --no-dev` for production, `composer install` for contributing/debugging
  * To use composer on [OVH](https://www.ovh.com), you have to do this first:
    * `alias php='/usr/local/php7.0/bin/php'`
    * `curl -sSL https://getcomposer.org/installer | php`

*hoplaJS* can be started from Composer (not for production) with:

* Production mode: `composer run`
* Development mode, on Linux: `composer run-dev`
* Development mode, on Windows: `composer cmd-dev`

Then, browse hoplaJS over [HTTP](http://localhost:8080) or [HTTPS](https://localhost:8443)



## Configure HoplaJS

* Running mode is determined by environment variable `APP_ENV`, which can takes following values: `dev` (development/debug) or `prod` (production, default mode)
* Legal information, such as details on hosting and website ownership, can be defined in file `res/local/legal.html` (see `res/local/legal.html.dist` for more information)
* Footer HTML code, such as tracking code, can be defined in file `res/local/footer.html` (see `res/local/footer.html.dist` for more information)



## Debug and advanced settings

Please see: [contribute and debug](Contribute.md).



## Update HoplaJS

1. `git pull`
2. `composer install --no-dev` for production, `composer install` for contributing/debugging
3. `rm -rf var/cache/twig/`

For a better stability of your HoplaJS instance, we recommend you always use *git tags* when deploying the source code.



## HoplaJS logs

Logs are stored inside `var/logs/`.