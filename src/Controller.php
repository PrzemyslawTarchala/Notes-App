<?php 

declare(strict_types = 1);

namespace App;

require_once("src/Request.php");
require_once("src/Exception/ConfigurationException.php");
require_once("src/Exception/NotFoundException.php");

use App\Request;
use App\Exception\ConfigurationException;
use App\Exception\NotFoundException;

require_once("src/Database.php");
require_once("src/View.php");

class Controller
{
	private const DEFAULT_ACTION = 'list';

	private static array $configuration = [];

	private Database $database;
	private Request $request;
	private View $view;

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
		switch($this->action()){ //zwraca to co znajude sie pod kluczem action w tablicy get lub default_action

			case 'create':
				$page = 'create';
				$data = $this -> getRequestPost();
		
				if(!empty($data)){
					$this->database->createNote([	//Tl. nr: 134 / 3:30
						'title' => $data['title'],
						'description' => $data['description'] 
					]);
					header('Location: /?before=created'); //Przekierowanie do strony glownej
					exit();
				}

				break;
		
			case 'show':
				$page = 'show';
				$noteId = (int) $this->request->getParam('id');

				if(!$noteId){
					header('Location: /?error=missingNoteId');
					exit;  //Trzeba użyć exita aby przerwać wykonywanie skryptu
				}

				try{
					$note = $this->database->getNote($noteId);
				} catch(NotFoundException $e){
					header('Location: /?error=noteNotFound');
					exit;
				}

				$viewParams = [
					'note' => $note
				];
				break;
		
			default:
				$page = 'list';
				$viewParams = [
					'notes' => $this->database->getNotes(),
					'before' => $this->request->getParam('before'),
					'error' => $this->request->getParam('error')
				];
				break;
		}
		$this -> view -> render($page, $viewParams ?? []);
	}

	private function action(): string
	{
		return $this->request->getParam('action', self::DEFAULT_ACTION); //pobierz dane z parametru 'action' w przeciwnym razie DEFAULT
	}
}