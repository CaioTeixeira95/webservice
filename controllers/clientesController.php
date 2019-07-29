<?php

class clientesController {

	// Lista todos os clientes para uma venda
	public function index() {

		$clientes = new Clientes();

		header("Content-Type: application/json");
		echo json_encode($clientes->listAll());

	}

}