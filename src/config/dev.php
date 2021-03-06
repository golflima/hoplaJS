<?php

/*
 * This file is part of hoplaJS.
 * See: <https://github.com/golflima/hoplaJS>.
 *
 * Copyright (C) 2017 Jérémy Walther <jeremy.walther@golflima.net>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Otherwise, see: <https://www.gnu.org/licenses/agpl-3.0>.
 */

//Symfony\Component\Debug\Debug::enable(); //Commented due to a bug in Symfony Twig Extensions ... 
$app['debug'] = true;

// Silex logs
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.level' => Monolog\Logger::DEBUG,
    'monolog.logfile' => __DIR__.'/../../var/logs/silex-dev.log',
    'monolog.name' => 'Silex'
));

// hoplaJS logs
$app['monolog.hoplajs'] = function ($app) {
    $log = new $app['monolog.logger.class']('hoplaJS');
    $handler = new Monolog\Handler\StreamHandler(__DIR__.'/../../var/logs/hoplajs-dev.log', Monolog\Logger::DEBUG);
    $log->pushHandler($handler);
    return $log;
};

// Debug tools of Silex
$app->register(new Silex\Provider\HttpFragmentServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__.'/../../var/cache/profiler',
));
