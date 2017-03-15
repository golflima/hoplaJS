# Contribute to hoplaJS

Everyone is more than welcome to contribute to *hoplaJS*.

Note: *this document is useful if you only want to debug it too.*



## Contribution policy

* Development of *hoplaJS* is made accordingly to [*git-xflow*](https://github.com/golflima/git-xflow) process, please respect it.



## Tools

* To update back-end dependences, run `composer update` you may need to edit the file `composer.json`)
* To update front-end dependences, run `bower update` (you may need to edit the file `bower.json`)
* To bundle all front-end resources, run `gulp`

To run one of the above commands in the docker container provided, when it is running:
`docker-compose exec web -t /bin/bash`