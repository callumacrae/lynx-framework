<?php
define("VIEW_PATH", __DIR__ . "/lynx/calculator/views");

require_once(__DIR__ . "/lynx/calculator/controllers/HelloController.php");
require_once(__DIR__ . "/lynx/calculator/controllers/CalcController.php");

use lynx\controllers\HelloController;
use lynx\controllers\CalcController;

// Request dispatcher
if ($_SERVER['PATH_INFO'] == "/hello")
{
	$controller = new HelloController();
	$controller->sayHello();
}
else if ($_SERVER['PATH_INFO'] == "/calc")
{
	$controller = new CalcController();
	$controller->index();
}
else if ($_SERVER['PATH_INFO'] == "/calc/results")
{
	$controller = new CalcController();
	$controller->results();
}
else
{
	echo "You must choose a page!";
}
?>
