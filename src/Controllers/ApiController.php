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

    public function proxy(Application $app, Request $request, $url, $contentType = "")
    {
        $url = base64_decode(str_pad(strtr($url, '-_', '+/'), strlen($url) % 4, '=', STR_PAD_RIGHT));
        $contentType = base64_decode(str_pad(strtr($contentType, '-_', '+/'), strlen($contentType) % 4, '=', STR_PAD_RIGHT));
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $request->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if($response === FALSE) {
            $response = new JsonResponse(array(
                'cURL error code' => curl_errno($ch),
                'cURL error message' => curl_error($ch),
                'url' => $url,
                'contentType' => $contentType,
                'headers' => $request->headers
            ), 404);
            curl_close($ch);
            return $response;
        }
        curl_close($ch);
        list($responseHeadersTmp, $responseContent) = preg_split('/(\r\n){2}/', $response, 2);
        $responseHeadersTmp = preg_split('/(\r\n){1}/', $responseHeadersTmp);
        $responseHeaders = array();
        foreach ($responseHeadersTmp as $name => $value) {
            $responseHeaders[$name] = $value;
        }
        if ($contentType != "") {
            $responseHeaders['Content-type'] = $contentType;
        }
        return new Response($responseContent, 200, $responseHeaders);
    }
}