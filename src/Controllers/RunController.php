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

class RunController
{
    public function raw(Application $app, Request $request, $data)
    {
        $script = HoplaJsScript::deserialize($data);
        $app['monolog']->info('"/raw" called by IP: "'.$request->getClientIp().'" to read application hash: "'.$script->getHash().'".');
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

    public function run(Application $app, Request $request, $data)
    {
        $script = HoplaJsScript::deserialize($data);
        $app['monolog']->info('"/run" called by IP: "'.$request->getClientIp().'" to read application hash: "'.$script->getHash().'".');
        return $app['twig']->render('run.html.twig', array(
            'request' => $request,
            'data' => $data,
            'script' => $script,
            'footer' => file_get_contents(__DIR__.'/../../res/local/footer.html')));
    }
}