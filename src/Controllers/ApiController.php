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
        $script = HoplaJsScript::deserialize($data);
        $app['monolog.hoplajs']->info('"/api/decode" called by IP: "'.$request->getClientIp().'" to read application hash: "'.$script->getHash().'".');
        return new JsonResponse($script);
    }

    public function encode(Application $app, Request $request)
    {
        $javascript = $request->get('javascript');
        $dependencies = preg_split('/[\s]+/', $request->get('dependencies'));
        $css = $request->get('css');
        $body = $request->get('body');
        $script = new HoplaJsScript($javascript, $dependencies, $css, $body);
        $hash = $script->getHash();
        $app['monolog.hoplajs']->info('"/api/encode" called by IP: "'.$request->getClientIp().'" to generate application hash: "'.$hash.'".');
        return new JsonResponse(array(
            'data' => $script->serialize(),
            'hash' => $hash,
            'baseUrl' => $request->getSchemeAndHttpHost().$request->getBasePath()));
    }

    public function proxy(Application $app, Request $request, $url, $contentType = "")
    {
        // Decode parameters (b64 to prevent web server to think these calls are for files ...)
        $url = base64_decode(str_pad(strtr($url, '-_', '+/'), strlen($url) % 4, '=', STR_PAD_RIGHT));
        $contentType = base64_decode(str_pad(strtr($contentType, '-_', '+/'), strlen($contentType) % 4, '=', STR_PAD_RIGHT));
        // Log
        $app['monolog.hoplajs']->info('"/proxy" called by IP: "'.$request->getClientIp().'" to get URL: "'.$url.'" with content-type: "'.$contentType.'".');
        // Get request headers
        $requestHeaders = array_map(function($value) {
            return trim(ucwords(strtolower(is_array($value) ? $value[0] : $value), "()- \t\r\n\f\v"));
        }, $request->headers->all());
        // Filter request headers
        $requestHeaders = array_diff_key($requestHeaders, array(
            'Host' => '', 'X-Proxy-Url' => ''
        ));
        $request->getClientIp() != '::1' && $requestHeaders['X-Forwarded-For'] = $request->getClientIp(); // May cause issues if used from localhost
        // Init cURL session
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, true);
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
                'cUrlErrorCode' => curl_errno($ch),
                'cUrlErrorMessage' => curl_error($ch),
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
            $header = preg_split('/:[[:blank:]]?/', $value, 2);
            count($header) == 2 && $responseHeaders[trim(ucwords(strtolower($header[0]), "()- \t\r\n\f\v"))] = $header[1];
        }
        // Filter response headers
        $responseHeaders = array_diff_key($responseHeaders, array(
            'Content-Security-Policy' => '', 'Host' => '', 'Location' => '', '(Transfer-Encoding)' => '', 'X-Xss-Protection' => ''
        ));
        // Add specific response headers
        $contentType != "" && $responseHeaders['Content-Type'] = $contentType;
        $responseHeaders['Access-Control-Allow-Origin'] = $request->getSchemeAndHttpHost().$request->getBasePath();
        $responseHeaders['Vary'] = 'Origin';
        $responseHeaders['X-Frame-Options'] = 'SAMEORIGIN';
        // Return the response, always with 200 OK code
        return new Response($responseContent, 200, $responseHeaders);
    }
}