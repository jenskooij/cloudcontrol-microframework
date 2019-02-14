<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;


use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class App
{
    private static $publicDir;
    private static $subfolders;
    private static $templateDir;

    /**
     * App constructor made private to disable instantiation
     */
    private function __construct()
    {
    }


    public static function cliServerServeResource(): bool
    {
        if (PHP_SAPI === 'cli-server'
            && self::isAllowedFileExtension()
            && file_exists(self::getPublicDir() . Request::getRequestUri())) {
            return true;    // serve the requested resource as-is.
        }
        return false;
    }

    private static function isAllowedFileExtension(): bool
    {
        return preg_match('/\.(?:js|ico|txt|gif|jpg|jpeg|png|bmp|css|html|htm|php|pdf|exe|eot|svg|ttf|woff|ogg|mp3|xml|map|scss|json)$/',
            Request::getRequestUri());
    }

    public static function prepare(string $publicDir): void
    {
        ob_start('\jenskooij\auth\util\GlobalFunctions::sanitizeOutput');
        session_start();
        self::$publicDir = $publicDir;

        $whoops = new Run;
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();

        ResponseHeaders::init();
    }

    public static function run(): void
    {
        Router::route();
        /** @var RouteRepresentingObject $routeRepresentingObject */
        /** @var Route $route */
        foreach (Router::getMatchedRoutes() as $routeRepresentingObject) {
            if ($routeRepresentingObject->route !== null) {
                $route = new $routeRepresentingObject->route;
                $route->run();
            }
        }
    }

    /**
     * @return string
     */
    public static function getPublicDir(): string
    {
        return self::$publicDir;
    }

    public static function getRootDir(): string
    {
        return realpath(self::getPublicDir() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
    }

    /**
     * @return string
     */
    public static function getSubfolders(): string
    {
        if (self::$subfolders === null) {
            if (PHP_SAPI === 'cli-server' || PHP_SAPI === 'cli') {
                self::$subfolders = '/';
            } else {
                $rootPath = str_replace('\\', '/',
                    realpath(App::getPublicDir() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));
                $subfolders = '/' . str_replace('//', '/',
                        str_replace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), "", $rootPath));
                $subfolders = str_replace('//', '/', $subfolders);
                self::$subfolders = str_replace('vendor/getcloudcontrol/cloudcontrol/', '', $subfolders);
            }
        }

        return self::$subfolders;
    }

    public static function getTemplateDir(): string
    {
        if (self::$templateDir === null) {
            self::$templateDir = realpath(self::getPublicDir() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);
        }
        return self::$templateDir;
    }

    public static function render(): void
    {
        Router::route();
        /** @var RouteRepresentingObject $routeRepresentingObject */
        /** @var Route $route */
        foreach (Router::getMatchedRoutes() as $routeRepresentingObject) {
            if ($routeRepresentingObject->template !== null) {
                Renderer::setTemplate($routeRepresentingObject->template);
                Renderer::render();
            }
        }
    }
}