<?php 
use MaplePHP\Foundation\Kernel\Kernel;
$dir = realpath(dirname(__FILE__) . '/..') . '/';
// This will change 
require_once("{$dir}vendor/maplephp/foundation/autoload.php");
require_once("{$dir}vendor/autoload.php");

$kernel = new Kernel($dir);
$kernel->run();