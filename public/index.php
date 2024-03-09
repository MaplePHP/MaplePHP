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

// You can utilize a different path technique by passing it to the kernel run method
// Example:
//$param = $kernel->getRequest()->getQueryParams();
//$kernel->run($param['page'] ?? "");
