<?php 

declare(strict_types=1);

namespace App;

class Request
{
	private array $get = [];
	private array $post = [];

	public function __construct(array $get, array $post)
	{
		$this->get = $get;
		$this->post = $post;
	}

	public function getParam(string $name, $default = null) //pobieranie danych z get'a
	{
		return $this->get[$name] ?? $default;
	}

	public function postParam(string $name, $default = null) //pobieranie danych z post'a
	{
		return $this->post[$name] ?? $default;
	}
} 