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

class HoplaJsScript implements \JsonSerializable
{
    private $javascript;
    private $dependencies;
    private $css;
    private $body;

    public function __construct($javascript = '', array $dependencies = array(), $css = '', $body = '')
    {
        $this->javascript = $javascript;
        $this->dependencies = $dependencies;
        $this->css = $css;
        $this->body = $body;
    }

    public function jsonSerialize()
    {
        $data = array();
        $this->javascript != '' && $data['j'] = $this->javascript;
        count($this->dependencies) > 0 && $this->dependencies[0] != '' && $data['d'] = $this->dependencies;
        $this->css != '' && $data['c'] = $this->css;
        $this->body != '' && $data['b'] = $this->body;
        return $data;
    }

    public static function jsonDeserialize($data)
    {
        is_array($data) || $data = get_object_vars($data);
        return new self(
            array_key_exists('j', $data) ? $data['j'] : '',
            array_key_exists('d', $data) && ! is_null($data['d']) ? $data['d'] : array(),
            array_key_exists('c', $data) ? $data['c'] : '',
            array_key_exists('b', $data) ? $data['b'] : '');
    }

    public function serialize()
    {
        $serialized = json_encode($this);
        $serialized = gzcompress($serialized, 9);
        if ($serialized === FALSE) {
            throw new \Exception('Error when compressing data.'); 
        }
        $serialized = rtrim(strtr(base64_encode($serialized), '+/', '-_'), '=');
        return $serialized;
    }

    public static function deserialize($data)
    {
        $deserialized = base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
        $deserialized = gzuncompress($deserialized);
        if ($deserialized === FALSE) {
            throw new \Exception('Error when uncompressing gzipped data.');
        }
        $deserialized = json_decode($deserialized);
        $deserialized = self::jsonDeserialize($deserialized);
        return $deserialized;
    }

    public function getJavascript()
    {
        return $this->javascript;
    }

    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function getCss()
    {
        return $this->css;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHash()
    {
        return sha1(json_encode($this));
    }
}