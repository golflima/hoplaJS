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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function proxy(Application $app, Request $request, $url, $contentType)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        list($responseHeaders, $responseContent) = preg_split('/(\r\n){2}/', $response, 2);
        if (!is_null($contentType)) {
            $responseHeaders['Content-type'] = $contentType;
        }
        return new Response($responseContent, 200, $responseHeaders);
    }
}