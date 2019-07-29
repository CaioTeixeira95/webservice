<?php

class Clientes extends Model {

	// Lista todos os clientes disponíveis para venda
	public function listAll() {

		$sql = "SELECT * FROM clientes";
		$sql = $this->pdo->query($sql);

		return $sql->rowCount() > 0 ? $sql->fetchAll(PDO::FETCH_ASSOC) : array();

	}

}