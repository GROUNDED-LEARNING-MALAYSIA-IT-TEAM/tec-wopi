<?php

use Cake\Core\Plugin;

/**
 * Test suite bootstrap for EaglenavigatorSystem/Wopi.
 *
 * This function is used to find the location of CakePHP whether CakePHP
 * has been installed as a dependency of the plugin, or the plugin is itself
 * installed as a dependency of an application.
 */
$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);

    throw new Exception("Cannot find the root of the application, unable to run tests");
};
$root = $findRoot(__FILE__);
unset($findRoot);

chdir($root);
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT', $root);
define('APP_DIR', 'App');
define('WEBROOT_DIR', 'webroot');
define('APP', ROOT . '/tests/App/');
define('CONFIG', ROOT . '/tests/config/');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('TESTS', ROOT . DS . 'tests' . DS);
define('TMP', ROOT . DS . 'tmp' . DS);
define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . '/vendor/cakephp/cakephp');
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

//TEST_FILE_PATH is in tests folder of plugin
//define('TEST_FILE_PATH_TEST', ROOT . DS . 'tests'  . DS . 'test_files' . DS);
define('BOOTSTRAP_LOADED', true);

require_once $root . '/vendor/autoload.php';

/**
 * Define fallback values for required constants and configuration.
 * To customize constants and configuration remove this require
 * and define the data required by your plugin here.
 */
require_once $root . '/vendor/cakephp/cakephp/tests/bootstrap.php';

if (file_exists($root . '/config/bootstrap.php')) {
    require $root . '/config/bootstrap.php';

    return;
}


if (file_exists($root . '/config/wopi.php')) {
    require $root . '/config/wopi.php';

    return;
}


//init router
\Cake\Routing\Router::reload();

Plugin::getCollection()->add(new \EaglenavigatorSystem\Wopi\Plugin([
    'path' => dirname(dirname(__FILE__)) . DS,
    'routes' => true
]));
if (file_exists($root . '/config/bootstrap.php')) {
    require $root . '/config/bootstrap.php';
}