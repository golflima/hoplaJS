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

namespace HoplaJs\Controllers;

use HoplaJs\Models\HoplaJsScript;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for routes /raw/* and /run/*.
 * @author      Jérémy Walther <jeremy.walther@golflima.net>
 * @copyright   2017 Jérémy Walther
 * @license     https://www.gnu.org/licenses/agpl-3.0 AGPL-3.0
 */
class RunController
{
    /**
     * Handler for route /raw/{data}.
     * @param   Application     $app        Silex hoplaJS application.
     * @param   Request         $request    Silex current request.
     * @param   string          $data       Serialized HoplaJsScript instance.
     * @return  Response        Response as a JavaScript file.
     * @throws  \Exception      Error when deserializing the HoplaJsScript instance.
     */
    public function raw(Application $app, Request $request, $data)
    {
        $script = HoplaJsScript::deserialize($data);
        $app['monolog.hoplajs']->info('"/raw" called.', array(
            'ip' => $request->getClientIp(),
            'hash' => $script->getHash()));
        return new Response($app['twig']->render(
            'raw.js.twig',
            array(
                'request' => $request,
                'data' => $data,
                'script' => $script)),
            200,
            array(
                'Content-Type' => 'text/javascript'
            ));
    }

    /**
     * Handler for route /run/{data}.
     * @param   Application     $app        Silex hoplaJS application.
     * @param   Request         $request    Silex current request.
     * @param   string          $data       Serialized HoplaJsScript instance.
     * @return  Response        Response as a HTML page.
     * @throws  \Exception      Error when deserializing the HoplaJsScript instance.
     */
    public function run(Application $app, Request $request, $data)
    {
        $script = HoplaJsScript::deserialize($data);
        $app['monolog.hoplajs']->info('"/run" called.', array(
            'ip' => $request->getClientIp(),
            'hash' => $script->getHash()));
        return $app['twig']->render('run.html.twig', array(
            'request' => $request,
            'data' => $data,
            'script' => $script,
            'footer' => file_get_contents(__DIR__.'/../../res/local/footer.html')));
    }
}