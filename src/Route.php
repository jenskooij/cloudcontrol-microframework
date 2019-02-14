<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;


abstract class Route
{
    /**
     * Runs the logic for the selected Route
     */
    abstract public function run():void;

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