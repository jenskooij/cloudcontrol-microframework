<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;


class NoopRoute implements Route
{

    /**
     * Runs the logic for the selected Route
     */
    public function run(): void
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