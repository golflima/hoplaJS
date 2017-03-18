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

// Get execution environment of HoplaJS
// It should always be 'prod' (set in web/.htaccess), but when contributing or debugging HoplaJS you should set it to 'dev'
defined('APP_ENV') || define('APP_ENV', (getenv('APP_ENV') ? getenv('APP_ENV') : 'prod'));

// Init Composer autoloader
require_once __DIR__.'/../vendor/autoload.php';

// Init Silex
$app = new Silex\Application();
// HoplaJS version
$app['hoplaJS_version'] = file_get_contents(__DIR__.'/../VERSION');
// Monolog factory
$app['monolog.factory'] = $app->protect(function ($name) use ($app) {
    $log = new $app['monolog.logger.class']($name);
    $log->pushHandler($app['monolog.handler']);
    return $log;
});
// asset() for Twig
$app->register(new Silex\Provider\AssetServiceProvider());
// Twig template engine
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    "twig.path" => __DIR__.'/Views',
    'twig.options' => array(
        'cache' => __DIR__.'/../var/cache/twig',
        'strict_variables' => true)
));
// Apply environment configuration
require __DIR__.'/config/'.(APP_ENV == 'dev' ? 'dev' : 'prod').'.php';
// Configure routes to controllers
require __DIR__.'/routes.php';
// Start the app
$app->run();