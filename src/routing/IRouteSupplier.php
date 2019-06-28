<?php
/**
 * Created by p284277 on 28-06-2019.
 */

namespace getcloudcontrol\microframework\routing;


interface IRouteSupplier
{
    public function getMatchedRoutesForRelativeUri(string $relativeUri) : array;
}