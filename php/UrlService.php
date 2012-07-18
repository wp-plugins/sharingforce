<?php

class Sharingforce_UrlService
{
    static public function bJsUrl()
    {
        return 'www.sharingforce.com/b/b.js';
    }

    static public function businessHomeUrl()
    {
        return 'https://www.sharingforce.com/business/';
    }

    static public function accountCreateUrl()
    {
        return 'https://www.sharingforce.com/account/create/';
    }

    static public function widgetEditUrl($widgetId)
    {
        return 'https://www.sharingforce.com/business/widgets/edit/?wid=' .
            $widgetId;
    }

    static public function widgetAddUrl()
    {
        return 'https://www.sharingforce.com/business/widgets/add/';
    }

    static public function appendParams($url, $additionalParams)
    {
        if (!$additionalParams) {
            return $url;
        }
        $params = explode('&', $additionalParams);
        foreach ($params as $param) {
            list($paramName) = explode('=', $param, 1);
            $url = self::removeParam($url, $paramName);
        }
        if ($url) {
            $paramsAndHash = explode('#', $url);
            if (false !== strpos($paramsAndHash[0], '?')) {
                $paramsAndHash[0] .= '&' . $additionalParams;
            } else {
                $paramsAndHash[0] .= '?' . $additionalParams;
            }
        } else {
            $paramsAndHash = array('#' . $additionalParams);
        }
        $result = $paramsAndHash[0];
        if (isset($paramsAndHash[1])) {
            $result .= '#' . $paramsAndHash[1];
        }
        return $result;
    }

    static public function removeParam($url, $paramName)
    {
        $start = strpos($url, '&' . $paramName);
        if (false === $start) {
            $start = strpos($url, '?' . $paramName);
        }
        if (false === $start) {
            $start = strpos($url, '#' . $paramName);
        }
        if (false === $start) {
            return $url;
        }
        $finish = 0;
        $l = strlen($url);
        for ($i = $start + strlen($paramName) + 1; $i < $l; $i++) {
            if('&' == $url[$i] || '#' == $url[$i]) {
                $finish = $i;
                break;
            }
        }
        if(!$finish) {
            return substr($url, 0, $start);
        }
        if ('?' == $url[$start] || '#' == $url[$start]) {
            $d = 1;
        } else {
            $d = 0;
        }
        return substr($url, 0, $start + $d) . substr($url, $finish + $d);
    }

}