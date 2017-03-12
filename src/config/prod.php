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
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.level' => Monolog\Logger::INFO,
    'monolog.logfile' => __DIR__.'/../../var/logs/hoplajs-prod.log',
    'monolog.name' => 'hoplaJS'
));