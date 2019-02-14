# Cloud Control Microframework
Skeleton Microframework for quickly building microservices

## Installation:
```
composer require getcloudcontrol/microframework
```

## Usage
### Recommended project structure
It is recommended to redirect all trafic to an index file withina "public" folder, so your composer.json and other
project files can remain in the root directory and be inaccessible remotely.
```
|
| .htaccess // Which redirects all trafic to public/index.php
| composer.json
| compsser.lock
|_ public/
|_ public/index.php
|_ vendor/
```

### Create an app
`index.php:`
```
<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');


App::prepare(__DIR__);
if (App::cliServerServeResource()) {
    return false;
}

App::run();
App::render();
```

### Run locally
Using PHP built-in server, using index.php as router script
```
php -S localhost:3000 index.php
```