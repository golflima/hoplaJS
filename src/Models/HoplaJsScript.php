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

namespace HoplaJs\Models;

/**
 * Represents an hoplaJS application.
 * @author      Jérémy Walther <jeremy.walther@golflima.net>
 * @copyright   2017 Jérémy Walther
 * @license     https://www.gnu.org/licenses/agpl-3.0 AGPL-3.0
 */
class HoplaJsScript implements \JsonSerializable
{
    /**
     * @var string    $javascript     The JavaScript to execute.
     * @var string[]  $dependencies   The URLs of all required dependencies of the JavaScript to execute.
     * @var string    $css            The CSS style to apply.
     * @var string    $body           The HTML body content to display and manipulate.
     */
    private $javascript, $dependencies, $css, $body;
    
    /**
     * Get a new instance of hoplaJS application.
     * @param string    $javascript     The JavaScript to execute.
     * @param string[]  $dependencies   The URLs of all required dependencies of the JavaScript to execute.
     * @param string    $css            The CSS style to apply.
     * @param string    $body           The HTML body content to display and manipulate.
     */
    public function __construct($javascript = '', array $dependencies = array(), $css = '', $body = '')
    {
        $this->javascript = $javascript;
        $this->dependencies = $dependencies;
        $this->css = $css;
        $this->body = $body;
    }

    /**
     * Returns a representation that can be converted to JSON.
     *
     * You should not call this method directly.
     *
     * @return mixed A representation that can be converted to JSON.
     */
    public function jsonSerialize()
    {
        // The aim is to make the shortest JSON representation
        // So, empty properties are skipped
        $data = array();
        $this->javascript != '' && $data['j'] = $this->javascript;
        count($this->dependencies) > 0 && $this->dependencies[0] != '' && $data['d'] = $this->dependencies;
        $this->css != '' && $data['c'] = $this->css;
        $this->body != '' && $data['b'] = $this->body;
        return $data;
    }

    /**
     * Deserializes a JSON representation into a new HoplaJsScript instance.
     *
     * You should not call this method directly.
     *
     * @param   mixed           A JSON representation.
     * @return  HoplaJsScript   Deserialized HoplaJsScript instance.
     */
    public static function jsonDeserialize($data)
    {
        is_array($data) || $data = get_object_vars($data);
        return new self(
            array_key_exists('j', $data) ? $data['j'] : '',
            array_key_exists('d', $data) && ! is_null($data['d']) ? $data['d'] : array(),
            array_key_exists('c', $data) ? $data['c'] : '',
            array_key_exists('b', $data) ? $data['b'] : '');
    }

    /**
     * Returns a serialized representation of this HoplaJsScript instance, which may be used in a URL.
     * @return string Serialized representation of this HoplaJsScript instance, which may be used in a URL.
     * @throws \Exception Error when compressing data.
     */
    public function serialize()
    {
        // 1. Encode in JSON
        $serialized = json_encode($this);
        // 2. Compress in GZip
        $serialized = gzcompress($serialized, 9);
        if ($serialized === FALSE) {
            throw new \Exception('Error when compressing data.'); 
        }
        // 3. Encode in Base64 URL-safe (https://tools.ietf.org/rfc/rfc4648.txt)
        $serialized = rtrim(strtr(base64_encode($serialized), '+/', '-_'), '=');
        return $serialized;
    }

    /**
     * Returns the deserialized HoplaJsScript instance represented by given string.
     * @param string $data An URL-compatible serialized HoplaJsScript instance.
     * @return HoplaJsScript The deserialized HoplaJsScript instance.
     * @throws \Exception Error when uncompressing gzipped data.
     */
    public static function deserialize($data)
    {
        // 1. Decode Base64 URL-safe (https://tools.ietf.org/rfc/rfc4648.txt)
        $deserialized = base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
        // 2. Decompress GZip
        $deserialized = gzuncompress($deserialized);
        if ($deserialized === FALSE) {
            throw new \Exception('Error when uncompressing gzipped data.');
        }
        // 3. Deserialize JSON
        $deserialized = json_decode($deserialized);
        // 4. Convert JSON representation into a HoplaJsScript instance
        $deserialized = self::jsonDeserialize($deserialized);
        return $deserialized;
    }

    /**
     * Gets the JavaScript to execute.
     * @return string The JavaScript to execute.
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * Gets the URLs of all required dependencies of the JavaScript to execute.
     * @return string[] The URLs of all required dependencies of the JavaScript to execute.
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Gets the CSS style to apply.
     * @return string The CSS style to apply.
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Gets the HTML body content to display and manipulate.
     * @return string The HTML body content to display and manipulate.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Gets the hash of this HoplaJsScript instance.
     * @return string The hash of this HoplaJsScript instance.
     */
    public function getHash()
    {
        return sha1(json_encode($this));
    }
}