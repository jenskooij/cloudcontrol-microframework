<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework\routing;


class NoopRoute extends Route
{

    /**
     * Runs the logic for the selected Route
     * @param array $uriMatches
     */
    public function run($uriMatches = []): void
    {
        // Doesn't do anything
    }

    /**
     * Returns the context, that will be exposed to the template
     * @return array
     */
    public function getContext(): array
    {
        return [];
    }
}