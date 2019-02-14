<?php
/**
 * Created by Jens on 14-2-2019.
 */

namespace getcloudcontrol\microframework;


class NoopRoute implements Route
{
    public function run(): void
    {
        // Doesnt do anything
    }
}