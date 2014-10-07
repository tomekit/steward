<?php

// Bootstrap before each Testcase

date_default_timezone_set('Europe/Prague');

require_once __DIR__ . '/../vendor/autoload.php';

// Set used environment variables as PHP constants to be used in tests
// TODO: rewrite to cycle to DRY
$browserName = getenv('BROWSER_NAME');
if (!$browserName) {
    throw new Exception('BROWSER_NAME environment variable must be defined');
}
define('BROWSER_NAME', $browserName);

$env = getenv('ENV');
if (!$env) {
    throw new Exception('ENV environment variable must be defined');
}
define('ENV', $env);

$serverUrl = getenv('SERVER_URL');
if (!$serverUrl) {
    throw new Exception('SERVER_URL environment variable must be defined');
}
define('SERVER_URL', $serverUrl);

$publishResults = getenv('PUBLISH_RESULTS');
if (!isset($publishResults)) {
    $publishResults = false;
}
define('PUBLISH_RESULTS', $publishResults);

$fixturesDir = getenv('FIXTURES_DIR');
if (!isset($fixturesDir)) {
    throw new Exception('FIXTURES_DIR environment variable must be defined');
}
define('FIXTURES_DIR', $fixturesDir);

$debug = getenv('DEBUG');
if (!isset($debug)) {
    $debug = false;
}
define('DEBUG', $debug);
