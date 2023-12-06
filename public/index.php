<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use MaplePHP\Foundation\Kernel\Kernel;
$dir = realpath(dirname(__FILE__) . '/..') . '/';
if(is_file("{$dir}app/Libraries/Foundation/autoload.php")) {
	require_once("{$dir}app/Libraries/Foundation/autoload.php");
}
if(is_file("{$dir}vendor/autoload.php")) {
	require_once("{$dir}vendor/autoload.php");
}
$kernel = new Kernel($dir);
$kernel->run();