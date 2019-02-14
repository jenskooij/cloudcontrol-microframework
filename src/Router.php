<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;

class Router
{

    public static function route()
    {
        $routeFile = App::getRootDir() . DIRECTORY_SEPARATOR . 'routes.json';
        if (!file_exists($routeFile)) {
            self::buildDefaultRoutesFile();
        }
    }

    private static function buildDefaultRoutesFile()
    {
        $routeFile = App::getRootDir() . DIRECTORY_SEPARATOR . 'routes.json';

        $defaultRoute = new \stdClass();
        $defaultRoute->relativeUri = '';
        $defaultRoute->route = '\getcloudcontrol\microframework\NoopRoute';
        $defaultRoute->template = 'index.twig';

        file_put_contents($routeFile, json_encode(array($defaultRoute), JSON_PRETTY_PRINT));
    }
}