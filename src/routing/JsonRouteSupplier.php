<?php
/**
 * Created by p284277 on 28-06-2019.
 */

namespace getcloudcontrol\microframework\routing;


use getcloudcontrol\microframework\App;

class JsonRouteSupplier implements IRouteSupplier
{

    const DEFAULT_ROUTES_JSON = 'routes.json';
    protected $routeFile;

    public function getMatchedRoutesForRelativeUri(string $relativeUri): array
    {
        $this->checkRouteFile();
        $routesArray = $this->getArrayOfRoutes();
        return array_filter(
            $routesArray,
            /**
             * @param $e RouteRepresentingObject
             * @return bool
             */
            function ($e) use ($relativeUri) {
                if ($this->isLikelyRegex($e->relativeUri)) {
                    return preg_match($e->relativeUri, $relativeUri, $e->matches);
                }
                return $e->relativeUri == $relativeUri;

            }
        );
    }

    protected function checkRouteFile(): void
    {
        if (!file_exists($this->getRouteFile())) {
            self::buildDefaultRoutesFile();
        }
    }

    /**
     * @return string
     */
    public function getRouteFile(): string
    {
        if ($this->routeFile === null) {
            $this->routeFile = App::getRootDir() . DIRECTORY_SEPARATOR . self::DEFAULT_ROUTES_JSON;
        }
        return $this->routeFile;
    }

    private function buildDefaultRoutesFile()
    {
        $defaultRoute = new RouteRepresentingObject();
        $defaultRoute->relativeUri = '';
        $defaultRoute->route = '\getcloudcontrol\microframework\routing\NoopRoute';
        $defaultRoute->template = 'index.twig';

        file_put_contents($this->getRouteFile(), json_encode(array($defaultRoute), JSON_PRETTY_PRINT));
    }

    /**
     * Converts the json routes to instances of RouteRepresentingObjects
     * based upon: https://stackoverflow.com/a/20654216/1666377
     *
     * @return array
     */
    private function getArrayOfRoutes(): array
    {
        $jsonString = file_get_contents(self::getRouteFile());

        $stdobj = json_decode($jsonString);
        $temp = serialize($stdobj);

        $temp = str_replace('O:8:"stdClass"', 'O:63:"\getcloudcontrol\microframework\routing\RouteRepresentingObject"',
            $temp);

        // Unserialize and walk away like nothing happend
        return unserialize($temp);
    }

    private function isLikelyRegex($string) {
        return preg_match("/^\/.+\/[a-z]*$/i",$string);
    }
}