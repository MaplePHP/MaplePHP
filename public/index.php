<?php 
use MaplePHP\Foundation\Kernel\Kernel;
$dir = realpath(dirname(__FILE__) . '/..') . '/';
// This will change (will tho only affect commits)
$autoload = "{$dir}app/Libraries/Foundation/autoload.php";
if (is_file("{$dir}vendor/maplephp/foundation/autoload.php")) {
	$autoload = "{$dir}vendor/maplephp/foundation/autoload.php";
}
require_once($autoload);
$kernel = new Kernel($dir);
$kernel->run();
