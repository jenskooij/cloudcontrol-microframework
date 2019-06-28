<?php
/**
 * Created by Jens on 21-2-2019.
 */

namespace getcloudcontrol\microframework\routing;


interface IRoute
{
    public function run($uriMatches = []): void;

    public function getContext(): array;
}