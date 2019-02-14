<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;

/**
 * Class Request
 * Series of helper methods for retrieval of information for this request
 *
 * @package jenskooij\auth\sys
 */
class Request
{
    const SERVER_ATTR_REQUEST_URI = 'REQUEST_URI';
    const SERVER_ATTR_QUERY_STRING = 'QUERY_STRING';
    const SERVER_ATTR_HTTPS = 'HTTPS';
    const SERVER_ATTR_SERVER_PORT = 'SERVER_PORT';
    const SERVER_ATTR_HTTP_X_FORWARDED_PROTO = 'HTTP_X_FORWARDED_PROTO';
    const SERVER_ATTR_REMOTE_ADDR = 'REMOTE_ADDR';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_HEAD = 'HEAD';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_OTHER = 'OTHER';
    protected static $requestUri;
    protected static $queryString;
    protected static $relativeUri;

    /**
     * A failsafe wrapper for retrieving the requestUri for this request
     *
     * @return string
     */
    public static function getRequestUri()
    {
        if (self::$requestUri === null) {
            self::$requestUri = isset($_SERVER[self::SERVER_ATTR_REQUEST_URI]) ? $_SERVER[self::SERVER_ATTR_REQUEST_URI] : '';
        }
        return self::$requestUri;

    }

    /**
     * A failsafe wrapper for retrieving the query string for this request
     *
     * @return string
     */
    public static function getQueryString(): string
    {
        if (self::$queryString === null) {
            self::$queryString = isset($_SERVER[self::SERVER_ATTR_QUERY_STRING]) ? $_SERVER[self::SERVER_ATTR_QUERY_STRING] : '';
        }
        return self::$queryString;
    }

    /**
     * Wheter or not the request is done over SSL
     *
     * @return bool
     */
    public static function isSecure(): bool
    {
        return
            (!empty($_SERVER[self::SERVER_ATTR_HTTPS]) && $_SERVER[self::SERVER_ATTR_HTTPS] !== 'off')
            || (isset($_SERVER[self::SERVER_ATTR_SERVER_PORT]) && $_SERVER[self::SERVER_ATTR_SERVER_PORT] === 443)
            || (isset($_SERVER[self::SERVER_ATTR_HTTP_X_FORWARDED_PROTO]) && $_SERVER[self::SERVER_ATTR_HTTP_X_FORWARDED_PROTO] === 'https');
    }

    /**
     * Wheter or not this request originates from localhost
     *
     * @return bool
     */
    public static function isLocalhost(): bool
    {
        $ipchecklist = array("localhost", "127.0.0.1", "::1");
        return (isset($_SERVER[self::SERVER_ATTR_REMOTE_ADDR]) && in_array($_SERVER[self::SERVER_ATTR_REMOTE_ADDR],
                $ipchecklist, true));
    }

    /**
     * Returns the relative Uri for this request. If this application
     * runs in a subfolder (see App::getSubfolders) returns only the part
     * of the requestUri after the subfolders
     *
     * @return string
     */
    public static function getRelativeUri(): string
    {
        if (self::$relativeUri === null) {
            if (App::getSubfolders() === '/') {
                self::$relativeUri = str_replace('?' . self::getQueryString(), '', substr(self::getRequestUri(), 1));
            } else {
                self::$relativeUri = str_replace('?' . self::getQueryString(), '',
                    str_replace(App::getSubfolders(), '', self::getRequestUri()));
            }
        }
        return self::$relativeUri;
    }

    /**
     * @return array|false
     */
    public static function getHeaders()
    {
        if (function_exists('apache_request_headers')) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            return apache_request_headers();

        } else {
            return self::getHeadersPolyfill();
        }
    }

    /**
     * Polyfill for when apache request headers are not set, for example
     * when not running with apache :P
     *
     * @return array
     */
    private static function getHeadersPolyfill()
    {
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach ($_SERVER as $key => $val) {
            if (preg_match($rx_http, $key)) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $rx_matches = explode('_', $arh_key);
                if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                    foreach ($rx_matches as $ak_key => $ak_val) {
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    }
                    $arh_key = implode('-', $rx_matches);
                }
                $arh[$arh_key] = $val;
            }
        }
        return ($arh);
    }

    public static function getRequestMethod()
    {
        $request_method = strtoupper(getenv('REQUEST_METHOD'));
        $http_methods = array(
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_PUT,
            self::METHOD_DELETE,
            self::METHOD_HEAD,
            self::METHOD_OPTIONS
        );
        if (!in_array($request_method, $http_methods)) {
            return self::METHOD_OTHER;
        }
        return $request_method;
    }

}