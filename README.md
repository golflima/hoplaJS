# hoplaJS

> A PHP web application running JavaScript code stored in URLs on client-side

## Installation

Assuming [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git),
[PHP](http://php.net/manual/en/install.php) and
[Composer](https://getcomposer.org/download/) are already installed on your server :

* [Clone](https://github.com/golflima/hoplaJS.git) or [download](https://github.com/golflima/hoplaJS/archive/master.zip) HoplaJS on your server
* Run `composer install --no-dev`
  * To use composer on OVH, you have to do this first:
    * `alias php='/usr/local/php7.0/bin/php'`
    * `curl -sS https://getcomposer.org/installer | php`
* (Optional) You may want to change the execution environment `APP_ENV` of HolpaJs, in `web/.htaccess`, from `prod` (default) to `dev`
* Start the server:
  * *php-cli*:
    * Production mode: `composer run`
    * Development mode, on Linux: `composer run-dev`
    * Development mode, on Windows: `composer cmd-dev`
  * [*Docker*](https://www.docker.com/get-docker):
    * `docker-compose up -d`
    * *If you use the docker container provided, you don't need to install Git, PHP or Composer on your computer.*

## License

[GNU AGPL v3.0](https://www.gnu.org/licenses/agpl-3.0.html)