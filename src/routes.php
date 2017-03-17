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
$app->get('/api/proxy/{url}', 'HoplaJs\\Controllers\\ApiController::proxy');
$app->get('/api/proxy/{url}/{contentType}', 'HoplaJs\\Controllers\\ApiController::proxy');
$app->get('/edit', 'HoplaJs\\Controllers\\SiteController::edit');
$app->get('/edit/{data}', 'HoplaJs\\Controllers\\SiteController::edit');
$app->get('/raw/{data}', 'HoplaJs\\Controllers\\RunController::raw');
$app->get('/run/{data}', 'HoplaJs\\Controllers\\RunController::run');

$app->error(function (\Exception $e, Symfony\Component\HttpFoundation\Request $request, $code) use ($app) {
    $hash = sha1($request.$e.microtime());
    $app['monolog']->error("Error page displayed.", array(
        'hash ' => $hash,
        'request' => $request,
        'exception' => $e,
        'code' => $code));
    return $app['twig']->render('error.html.twig', array(
        'request' => $request,
        'e' => $e,
        'code' => $code,
        'hash' => $hash,
        'footer' => file_get_contents(__DIR__.'/../../res/local/footer.html')));
});