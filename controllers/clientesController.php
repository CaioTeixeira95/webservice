<?php

class clientesController {

	public function index() {

		$clientes = new Clientes();

		header("Content-Type: application/json");
		echo json_encode($clientes->listarTodos());

	}

}