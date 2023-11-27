<?php 

declare(strict_types = 1);

// namespace App;

spl_autoload_register(function (string $classNamespace) {
	$path = str_replace(['\\', 'App/'], ['/', ''], $classNamespace); 
	$path = "src/$path.php";
	require_once($path);
});

require_once("src/Utils/debug.php");
$configuration = require_once("config/config.php");

use App\Controller\AbstractController;
use App\Controller\NoteController;
use App\Request;
use App\Exception\AppException;
use App\Exception\ConfigurationException;

$request = new Request($_GET, $_POST, $_SERVER); //Tablice globalne

try{
	//$controller = new Controller($request);
	//$controller -> run();

	AbstractController::initConfiguration($configuration);
	(new NoteController($request)) -> run(); //to samo
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
  // echo '<h3>' . $e->getPrevious()->getMessage() .'</h3>';
}

catch(\Throwable $e)
{
	echo '<h1>Wystąpił błąd aplikacji</h1>'; 
	dump($e);
}

 