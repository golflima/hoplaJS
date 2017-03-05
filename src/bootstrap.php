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
APP_ENV == 'dev' && Symfony\Component\Debug\Debug::enable();
APP_ENV == 'prod' && ini_set('display_errors', 0);

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['hoplaJS_version'] = json_decode(file_get_contents(__DIR__.'/../composer.json'))->version;
$app->register(new Silex\Provider\AssetServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    "twig.path" => __DIR__.'/Views',
    'twig.options' => array('cache' => __DIR__.'/../var/cache/twig', 'strict_variables' => true)
));
require __DIR__.'/config/'.(APP_ENV == 'prod' ? 'prod' : 'dev').'.php';
require __DIR__.'/routes.php';
$app->run();