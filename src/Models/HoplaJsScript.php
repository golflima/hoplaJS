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
    private $htmlBody;

    public function __construct($javascript = "", array $dependencies = array(), $htmlBody = "")
    {
        $this->javascript = $javascript;
        $this->dependencies = $dependencies;
        $this->htmlBody = $htmlBody;
    }

    public function jsonSerialize()
    {
        $data = array();
        $data['j'] = $this->javascript;
        $data['d'] = $this->dependencies;
        $data['b'] = $this->htmlBody;
        return $data;
    }

    public static function jsonDeserialize($data)
    {
        return new self(
            $data->j,
            is_null($data->d) ? array() : $data->d,
            is_null($data->b) ? array() : $data->b);
    }

    public function serialize()
    {
        $serialized = json_encode($this);
        $serialized = gzcompress($serialized, 9);
        if ($serialized === FALSE) {
            throw new \Exception("Error when compressing data."); 
        }
        $serialized = rtrim(strtr(base64_encode($serialized), '+/', '-_'), '=');
        return $serialized;
    }

    public static function deserialize($data)
    {
        $deserialized = base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
        $deserialized = gzuncompress($deserialized);
        if ($deserialized === FALSE) {
            throw new \Exception("Error when uncompressing gzipped data.");
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

    public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    public function getHash()
    {
        return sha1(json_encode($this));
    }
}