<?php

class Model {

	protected $pdo;

	public function __construct() {
		global $pdo;
		$this->pdo = $pdo;
	}

}