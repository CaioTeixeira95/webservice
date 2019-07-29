<?php

class produtosController {

	// Lista todos os produtos para uma venda
	public function index() {

		$produtos = new Produtos();

		header("Content-Type: application/json");
		echo json_encode($produtos->listAll());

	}

}