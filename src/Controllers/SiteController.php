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
 * Controller for routes / and /edit/*.
 * @author      Jérémy Walther <jeremy.walther@golflima.net>
 * @copyright   2017 Jérémy Walther
 * @license     https://www.gnu.org/licenses/agpl-3.0 AGPL-3.0
 */
class SiteController
{
    /**
     * Handler for routes /, /edit and /edit/{data}.
     * @param   Application     $app        Silex hoplaJS application.
     * @param   Request         $request    Silex current request.
     * @param   string          $data       Serialized HoplaJsScript instance.
     * @return  Response        Response as a HTML page.
     * @throws  \Exception      Error when deserializing the HoplaJsScript instance.
     */
    public function edit(Application $app, Request $request, $data = null)
    {
        $script = is_null($data) ? new HoplaJsScript() : HoplaJsScript::deserialize($data);
        return $app['twig']->render('edit.html.twig', array(
            'request' => $request,
            'script' => $script,
            'dependencies' => implode("\n", $script->getDependencies()),
            'footer' => file_get_contents(__DIR__.'/../../res/local/footer.html'),
            'legal' => file_get_contents(__DIR__.'/../../res/local/legal.html')
            ));
    }
}