<?php

class Clientes extends Model {

	public function listarTodos() {

		$sql = "SELECT * FROM clientes";
		$sql = $this->pdo->query($sql);

		return $sql->rowCount() > 0 ? $sql->fetchAll(PDO::FETCH_ASSOC) : array();

	}

}