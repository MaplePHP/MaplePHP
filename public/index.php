<?php 
use MaplePHP\Foundation\Kernel\Kernel;

$publicDir = dirname(__FILE__) . '/';
$dir = realpath($publicDir . '..') . '/';

// This will change (will tho only affect commits)
$autoload = "{$dir}app/Libraries/Foundation/autoload.php";
if (is_file("{$dir}vendor/maplephp/foundation/autoload.php")) {
	$autoload = "{$dir}vendor/maplephp/foundation/autoload.php";
}

require_once($autoload);
$kernel = new Kernel($publicDir, $dir);
$kernel->run();

// You can utilize a different path technique by passing it to the kernel run method
// Example:
//$param = $kernel->getRequest()->getQueryParams();
//$kernel->run($param['page'] ?? "");
