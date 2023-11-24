<?php 

declare(strict_types = 1);

namespace App;

use App\Exception\AppException;
use App\Exception\ConfigurationException;
use Throwable;

require_once("src/Utils/debug.php");
require_once("src/Controller.php");
require_once("src/Exception/AppException.php");

$configuration = require_once("config/config.php");

$request = [
	'get' => $_GET,
	'post'=> $_POST
];

try{
	//$controller = new Controller($request);
	//$controller -> run();

	Controller::initConfiguration($configuration);
	(new Controller($request)) -> run(); //to samo
} 

catch(ConfigurationException $e)
{
	echo '<h1>Wystąpił błąd aplikacji</h1>';
	echo 'Problem z konfiguracja. Prosze skontaktowac sie z administratorem.';
}

catch(AppException $e)
{
	echo '<h1>Wystąpił błąd aplikacji</h1>';
	echo '<h3>' . $e->getMessage() .'</h3>';
  echo '<h3>' . $e->getPrevious()->getMessage() .'</h3>';
}

catch(Throwable $e)
{
	echo '<h1>Wystąpił błąd aplikacji</h1>'; 
}

 