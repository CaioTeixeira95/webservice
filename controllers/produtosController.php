<?php

class produtosController {

	public function index() {

		$produtos = new Produtos();

		header("Content-Type: application/json");
		echo json_encode($produtos->listAll());

	}

}