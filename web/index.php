<?php


/**
 * ----------------------------------------------------
 * Request time start per session
 * we will use this time to control user request flow
 * ----------------------------------------------------
 */

define('REQUEST_START', microtime(true));

/**
 *  Autoload Packages and Container
 *  Register services && load all packages
 */
require __DIR__ . '/../vendor/autoload.php';


require __DIR__ . '/../src/Container/container.php';

/**
 *  Parse Requests
 *  dispatch all requested endpoint
 */

$app = include __DIR__ . '/../src/Routes/api.php';

$app->dispatch();


