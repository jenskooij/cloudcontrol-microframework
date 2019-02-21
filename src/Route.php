<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;


abstract class Route implements IRoute
{
    /**
     * Runs the logic for the selected Route
     * @param array $uriMatches
     */
    abstract public function run($uriMatches = []):void;

    /**
     * Returns the context, that will be exposed to the template
     * Implementation hook
     *
     * @return array
     */
    public function getContext():array
    {
        return [];
    }
}