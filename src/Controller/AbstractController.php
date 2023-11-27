<?php 

declare(strict_types=1);

namespace App\Controller;

use App\Database;
use App\Request;
use App\View;
use App\Exception\ConfigurationException;

abstract class AbstractController
{
	protected const DEFAULT_ACTION = 'list';

	private static array $configuration = [];

	protected Database $database;
	protected Request $request;
	protected View $view;

	public static function initConfiguration(array $configuration): void 
	{
		self::$configuration = $configuration;
	}

	public function __construct(Request $request)
	{
		if (empty(self::$configuration['db'])){
			throw new ConfigurationException('Configuration error');
		}
		$this->database = new Database(self::$configuration['db']);

		$this -> request = $request;
		$this -> view = new View();
	}

	public function run(): void
	{
		$action = $this->action() . 'Action';
		if(!method_exists ($this, $action)){ //$This zwraca obiek na którym operujemy -> help(method_exists)
			$action = self::DEFAULT_ACTION . 'Action';
		} else {
			$this->$action(); //w tym przypadku jezeli $action = 'create' to $action() = create(); analogicznie show i list
		}
	}

	protected function redirect(string $to, array $params): void
	{
		$location = $to;

		if(count($params)){
			$queryParams = [];
			foreach($params as $key => $value){
				$queryParams[] = urlencode($key) . '=' . urlencode($value);
			}
			$queryParams = implode('&', $queryParams);
			$location .= '?' . $queryParams;
		}
		  
		header("Location: $location");
		exit;
	}

	private function action(): string
	{
		return $this->request->getParam('action', self::DEFAULT_ACTION); //pobierz dane z parametru 'action' w przeciwnym razie DEFAULT
	}
}