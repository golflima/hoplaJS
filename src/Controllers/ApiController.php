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
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController
{
    public function decode(Application $app, Request $request, $data)
    {
        return new JsonResponse(HoplaJsScript::deserialize($data));
    }

    public function encode(Application $app, Request $request)
    {
        $javascript = $request->get('javascript');
        $dependencies = preg_split('/[\s]+/', $request->get('dependencies'));
        $htmlBody = $request->get('htmlBody');
        $script = new HoplaJsScript($javascript, $dependencies, $htmlBody);
        return new JsonResponse(array(
            'data' => $script->serialize(),
            'hash' => $script->getHash(),
            'baseUrl' => $request->getSchemeAndHttpHost().$request->getBasePath()));
    }

    // TODO : add "proxy"
    // TODO : add "json"
}