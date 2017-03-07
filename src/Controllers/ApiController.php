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
        // Decode parameters (b64 to prevent web server to think these calls are for files ...)
        $url = base64_decode(str_pad(strtr($url, '-_', '+/'), strlen($url) % 4, '=', STR_PAD_RIGHT));
        $contentType = base64_decode(str_pad(strtr($contentType, '-_', '+/'), strlen($contentType) % 4, '=', STR_PAD_RIGHT));
        // Init cURL session
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $requestHeaders = array_map(function($value) {
            return is_array($value) ? $value[0] : $value;
        }, $request->headers->all());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 seconds allowed to do the download
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, function(
            $handle, $downloadSize, $downloaded, $uploadSize, $uploaded) {
                return max($downloadSize, $downloaded) > (10 * 1024**2);
        }); // Disallow the download of files bigger than 10 Mio
        $response = curl_exec($ch);
        if($response === FALSE) {
            // Handle errors
            $response = new JsonResponse(array(
                'cURL error code' => curl_errno($ch),
                'cURL error message' => curl_error($ch),
                'url' => $url,
                'contentType' => $contentType,
                'headers' => $requestHeaders
            ), 404);
            curl_close($ch);
            return $response;
        }
        curl_close($ch);
        // Split headers from content
        list($responseHeadersTmp, $responseContent) = preg_split('/(\r\n){2}/', $response, 2);
        // Parse headers
        $responseHeadersTmp = preg_split('/\r\n/', $responseHeadersTmp);
        $responseHeaders = array();
        foreach ($responseHeadersTmp as $value) {
            $header = preg_split('/:[[:blank:]]/', $value);
            count($header) == 2 && $responseHeaders[$header[0]] = $header[1];
        }
        if ($contentType != "") {
            $responseHeaders['Content-Type'] = $contentType;
        }
        return new Response($responseContent, 200, $responseHeaders);
    }
}