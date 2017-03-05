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

class SiteController
{
    public function edit(Application $app, Request $request, $data = null)
    {
        $script = is_null($data) ? new HoplaJsScript() : HoplaJsScript::deserialize($data);
        return $app['twig']->render('edit.html.twig', array('request' => $request, 'script' => $script, 'dependencies' => implode("\n", $script->getDependencies())));
    }
}