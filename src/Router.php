<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;

use ReflectionClass;

class Router
{
    protected static $routeFile;
    protected static $matchedRoutes;
    protected static $routed = false;

    public static function route()
    {
        if (self::$routed === false) {
            self::checkRouteFile();
            $arrayOfRoutes = self::getArrayOfRoutes();

            self::$matchedRoutes = array_filter(
                $arrayOfRoutes,
                /**
                 * @param $e RouteRepresentingObject
                 * @return bool
                 */
                function ($e) {
                    if (self::isLikelyRegex($e->relativeUri)) {
                        return preg_match($e->relativeUri, Request::getRelativeUri(), $e->matches);
                    }
                    return $e->relativeUri == Request::getRelativeUri();

                }
            );
            self::$routed = true;
        }
    }

    private static function isLikelyRegex($string) {
        return preg_match("/^\/.+\/[a-z]*$/i",$string);
    }

    private static function buildDefaultRoutesFile()
    {
        $defaultRoute = new RouteRepresentingObject();
        $defaultRoute->relativeUri = '';
        $defaultRoute->route = '\getcloudcontrol\microframework\NoopRoute';
        $defaultRoute->template = 'index.twig';

        file_put_contents(self::getRouteFile(), json_encode(array($defaultRoute), JSON_PRETTY_PRINT));
    }

    /**
     * @return string
     */
    public static function getRouteFile():string
    {
        if (self::$routeFile === null) {
            self::$routeFile = App::getRootDir() . DIRECTORY_SEPARATOR . 'routes.json';
        }
        return self::$routeFile;
    }

    protected static function checkRouteFile(): void
    {
        if (!file_exists(self::getRouteFile())) {
            self::buildDefaultRoutesFile();
        }
    }

    /**
     * Converts the json routes to instances of RouteRepresentingObjects
     * based upon: https://stackoverflow.com/a/20654216/1666377
     *
     * @return array
     */
    private static function getArrayOfRoutes():array
    {
        $jsonString = file_get_contents(self::getRouteFile());

        $stdobj = json_decode($jsonString);
        $temp = serialize($stdobj);

        $temp = str_replace('O:8:"stdClass"', 'O:55:"\getcloudcontrol\microframework\RouteRepresentingObject"', $temp);

        // Unserialize and walk away like nothing happend
        return unserialize($temp);
    }

    /**
     * @return mixed
     */
    public static function getMatchedRoutes()
    {
        return self::$matchedRoutes;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public static function getAllAvailableRoutes()
    {
        $classes = get_declared_classes();
        $implementsIRoute = array();
        foreach($classes as $klass) {
            $reflect = new ReflectionClass($klass);
            if($reflect->implementsInterface('\getcloudcontrol\microframework\IRoute')) {
                $implementsIRoute[] = $klass;
            }
        }
        return $implementsIRoute;
    }
}