# ![](../web/assets/images/favicon_32.png) hoplaJS - Contribute

Everyone is more than welcome to contribute to *hoplaJS*.

Note: *this document is useful if you only want to debug it too.*



## First steps

1. [Install HoplaJS](Install.md)
2. [Test it](Usage.md)
3. Read this page



## Contribution policy

* Development of *hoplaJS* is made accordingly to [*git-xflow*](https://github.com/golflima/git-xflow) process, please respect it.
* Before proposing any change to HoplaJS, please make sure you have correctly tested it.



## Tools

* To update back-end dependences, run `composer update` you may need to edit the file `composer.json`)
* To update front-end dependences, run `bower update` (you may need to edit the file `bower.json`)
* To bundle all front-end resources, run `gulp`

To run one of the above commands in the docker container provided, when it is running:
`docker-compose exec web -t /bin/bash`



## Switch between Development and Production mode

By default, HoplaJS runs on Production mode. Running mode is determined by the environment variable `APP_ENV`:

* `APP_ENV=dev`: Development mode
  * More logs, debug bar and web profiler on client-side, display errors
* `APP_ENV=prod`: Production mode
  * Less logs (1-year retention), no debug information available

This can be done in the `.htaccess` file with the `SetEnv APP_ENV dev` or `SetEnv APP_ENV prod` directives.
Or it can be achieved by setting the environment variable `APP_ENV` directly on the server too.