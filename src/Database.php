<?php 

declare(strict_types=1);

namespace App;

use App\Exception\ConfigurationException;
use App\Exception\StorageException;
use App\Exception\NotFoundException;
use PDO;
use PDOException;
use Throwable;

class Database
{

private PDO $conn;

	public function __construct(array $config)
	{
		try{
			$this -> validateConfig($config);
			$this -> createConnection($config);
		} catch(PDOException $e){
			throw new StorageException('Connection error');
		}
	}

	public function getNote(int $id): array
	{
		try{

			$query = "SELECT * FROM notes WHERE id = $id";
			$result = $this->conn->query($query);
			$note = $result->fetch(PDO::FETCH_ASSOC);
		}catch(Throwable $e){
			throw new StorageException ('Nie udalo pobrać się notatki', 400, $e);
		}	

		if(!$note){
			throw new NotFoundException("Notatka o id: $id nie istnieje");
		}

		return $note;
	}

	public function getNotes(int $pageNumber, int $pageSize, string $sortBy, string $sortOrder): array
	{
		try{

			$limit = $pageSize;
			$offset = ($pageNumber - 1) * $pageSize;

			if (!in_array($sortBy, ['created', 'title'])) {
				$sortBy = 'title';
			}

			if (!in_array($sortOrder, ['asc', 'desc'])) {
				$sortOrder = 'desc';
			}

			//Pierwsza zmienna - po czym chcemy wyszukiwac, druga - kierunek 
			//LIMIT 5 -> pobierz 5 elementow, LIMIT 0, 5 - pobierz od 0 do 5 elementu 
			$query = "
				SELECT id, title, created 
				FROM notes
				ORDER BY $sortBy $sortOrder  
				LIMIT $offset, $limit
			";
			
			
			$result = $this->conn->query($query);
			return $result->fetchAll(PDO::FETCH_ASSOC);
			// foreach($result as $row){  To samo co linia wyżej
			// 	$notes[] = $row;
			// }

		}catch(Throwable $e){
			throw new StorageException('Nie udało się pobrać danych o notatkach', 400, $e);
		}
	}

	public function getCount(): int
	{
		try{
			$query = "SELECT count(*) AS cn FROM notes"; //zwraca liczbe recordow w tabeli notes pod nazwą 'cn'
			$result = $this->conn->query($query);
			$result =  $result->fetch(PDO::FETCH_ASSOC);
			if($result === false){
				throw new StorageException('Błąd przy próbie pobrania ilości notatek', 400, $e);
			}
			return (int) $result['cn'];
		} catch(Throwable $e){
			throw new StorageException('Nie udało się pobrać informacji o liczbie notetek', 400, $e);
		}
	}

	public function createNote(array $data): void
	{
		try{
			$title = $this->conn->quote($data['title']);
			$description =  $this->conn->quote($data['description']);
			$created = $this->conn->quote(date('Y-m-d H:i:s'));

			$query = "
				INSERT INTO notes(title, description, created) 
				VALUES($title, $description, $created)
				";
			
			$this->conn->exec($query); //zapytanie do bazy danych 
		} catch(Throwable $e){
			throw new StorageException('Nie udalo się utworzyć notatki', 400, $e);
		}
	}

	public function editNote(int $id, array $data): void
	{
		try{
			$title = $this->conn->quote($data['title']);
			$description = $this->conn->quote($data['description']);
			$query = "
				UPDATE notes 
				SET title = $title, description = $description
				WHERE id = $id
			";

			$this->conn->exec($query); //Potrzebne do zaktualizowania bazy danych
		}	catch(Throwable $e){
			throw new StorageException('Nie udalo się zaktualizować notatki', 400, $e);
		}
	}

	public function deleteNote(int $id): void
	{
		try{
			$query = "DELETE FROM notes WHERE id = $id LIMIT 1";
			$this->conn->exec($query);
		} catch (Throwable $e){
			throw new StorageException ('Nie udalo sie usunac notatki', 400, $e);
		}
	}

	private function createConnection(array $config): void
	{
		$dsn = "mysql:dbname={$config['database']};host={$config['host']}";
		$this->conn = new PDO(
			$dsn,
			$config['user'],
			$config['password'],
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //Przy polaczeniu ustwaione wszystkie "error'y" będa traktowane jako "exception"
			]
		);
	}

	private function validateConfig(array $config): void
	{
		if(empty($config['database']) || empty($config['host']) || empty($config['user']) || empty($config['password'])){
			throw new ConfigurationException('Storage configuration error');
		}
	}
}