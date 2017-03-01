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

ini_set('display_errors', 0);

$loader = require_once __DIR__.'/../vendor/autoload.php';
$loader->add("app", dirname(__DIR__));

$app = new Silex\Application();
// require __DIR__.'/../config/prod.php';
// require __DIR__.'/../app/controllers.php';
$app->run();

return $app;