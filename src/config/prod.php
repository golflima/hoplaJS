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

// Ensure PHP is not showing any errors
ini_set('display_errors', 0);

// Silex logs
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.name' => 'Silex'
));
$app['monolog']->pushHandler(new Monolog\Handler\RotatingFileHandler(
    __DIR__.'/../../var/logs/silex-prod.log', 
    7, // We only keep logs for 1 week, these ones are only for debug
    Monolog\Logger::INFO));

// hoplaJS logs
$app['monolog.hoplajs'] = function ($app) {
    $log = new $app['monolog.logger.class']('hoplaJS');
    $handler = new Monolog\Handler\RotatingFileHandler(
        __DIR__.'/../../var/logs/hoplajs-prod.log', 
        365, // We only keep logs for 1 year, accordingly to French laws
        Monolog\Logger::INFO);
    $log->pushHandler($handler);
    return $log;
};