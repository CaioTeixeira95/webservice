<?php

class Produtos extends Model {

	public function listAll() {

		$sql = "SELECT * FROM produtos";
		$sql = $this->pdo->query($sql);

		return $sql->rowCount() > 0 ? $sql->fetchAll(PDO::FETCH_ASSOC) : array();

	}

}