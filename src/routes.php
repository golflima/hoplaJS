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

$app->get('/', 'HoplaJs\\Controllers\\SiteController::edit');
$app->post('/api/decode', 'HoplaJs\\Controllers\\ApiController::decode');
$app->get('/api/decode/{data}', 'HoplaJs\\Controllers\\ApiController::decode');
$app->post('/api/encode', 'HoplaJs\\Controllers\\ApiController::encode');
$app->get('/edit', 'HoplaJs\\Controllers\\SiteController::edit');
$app->get('/edit/{data}', 'HoplaJs\\Controllers\\SiteController::edit');
$app->get('/raw/{data}', 'HoplaJs\\Controllers\\RunController::raw');
$app->get('/run/{data}', 'HoplaJs\\Controllers\\RunController::run');
