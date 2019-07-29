<?php

class Produtos extends Model {

	// Lista todos os itens desponíveis para venda
	public function listAll() {

		$sql = "SELECT * FROM produtos";
		$sql = $this->pdo->query($sql);

		return $sql->rowCount() > 0 ? $sql->fetchAll(PDO::FETCH_ASSOC) : array();

	}

}