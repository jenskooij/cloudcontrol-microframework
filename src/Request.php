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
            if (self::getSubfolders() === '/') {
                self::$relativeUri = str_replace('?' . self::getQueryString(), '', substr(self::getRequestUri(), 1));
            } else {
                self::$relativeUri = str_replace('?' . self::getQueryString(), '',
                    str_replace(App::getSubfolders(), '', self::getRequestUri()));
            }
        }
        return self::$relativeUri;
    }
}