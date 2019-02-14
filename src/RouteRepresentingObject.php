<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;


class RouteRepresentingObject
{
    /**
     * @var $relativeUri string Relative uri that will be matched
     */
    public $relativeUri;
    /**
     * @var $route string Route Object that will be run
     */
    public $route;
    /**
     * @var $template string Optional name of the template to be rendered
     */
    public $template;
    /**
     * @var array If this was a route with a regular expression, this is where the matches will end up
     */
    public $matches = array();
}