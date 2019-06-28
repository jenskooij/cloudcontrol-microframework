<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;


use Exception;
use getcloudcontrol\microframework\routing\IRouteSupplier;
use getcloudcontrol\microframework\routing\JsonRouteSupplier;
use getcloudcontrol\microframework\routing\Router;
use Tracy\Debugger;

class App
{
    const DEFAULT_TEMPLATE_DIR_NAME = 'templates';
    const DEFAULT_LOGS_DIR_NAME = 'logs';

    private static $publicDir;
    private static $subfolders;
    private static $templateDir;
    private static $routeSupplier;

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
        self::$publicDir = $publicDir;
        self::initializeDebugger();

        if (Debugger::detectDebugMode()) {
            ob_start();
        } else {
            ob_start('\jenskooij\auth\util\GlobalFunctions::sanitizeOutput');
        }

        session_start();


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
                $route->run(isset($routeRepresentingObject->matches) ? $routeRepresentingObject->matches : []);
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

    /**
     * @return string
     * @throws Exception
     */
    public static function getTemplateDir(): string
    {
        if (self::$templateDir === null) {
            self::$templateDir = realpath(self::getPublicDir() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::DEFAULT_TEMPLATE_DIR_NAME . DIRECTORY_SEPARATOR);
            if (empty(self::$templateDir) || !is_dir(self::$templateDir)) {
                throw new Exception(sprintf("Template dir \"%s\" does not exist.", self::DEFAULT_TEMPLATE_DIR_NAME));
            }
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
                $context = [];
                if ($routeRepresentingObject->route !== null) {
                    $route = new $routeRepresentingObject->route;
                    $context = $route->getContext();
                }
                Renderer::render($context);
            }
        }
    }

    protected static function initializeDebugger(): void
    {
        Debugger::enable();
        Debugger::$strictMode = true;
        Debugger::$logDirectory = self::getRootDir() . DIRECTORY_SEPARATOR . self::DEFAULT_LOGS_DIR_NAME;
    }

    /**
     * @return IRouteSupplier
     */
    public static function getRouteSupplier(): IRouteSupplier
    {
        if (self::$routeSupplier === null) {
            self::$routeSupplier = new JsonRouteSupplier();
        }
        return self::$routeSupplier;
    }

    /**
     * @param IRouteSupplier $routeSupplier
     */
    public static function setRouteSupplier(IRouteSupplier $routeSupplier): void
    {
        self::$routeSupplier = $routeSupplier;
    }
}