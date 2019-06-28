<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework\routing;

use getcloudcontrol\microframework\App;
use getcloudcontrol\microframework\Request;
use ReflectionClass;

class Router
{
    protected static $routeFile;
    protected static $matchedRoutes;
    protected static $routed = false;

    public static function route()
    {
        if (self::$routed === false) {
            self::$matchedRoutes = App::getRouteSupplier()->getMatchedRoutesForRelativeUri(Request::getRelativeUri());
            self::$routed = true;
        }
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
        foreach ($classes as $klass) {
            $reflect = new ReflectionClass($klass);
            if ($reflect->implementsInterface('\getcloudcontrol\microframework\routing\IRoute')) {
                $implementsIRoute[] = $klass;
            }
        }
        return $implementsIRoute;
    }
}