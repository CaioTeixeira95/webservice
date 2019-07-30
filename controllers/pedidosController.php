<?php

class pedidosController {

	public function index() {}

	// Lista todos os pedidos feitos
	public function listar() {

		$pedidos = new Pedidos();

		header("Content-Type: application/json");
		echo json_encode($pedidos->listAll());
		
	}

	public function itens() {

		$data = file_get_contents("php://input");

		if (!empty($data)) {

			$itens = new Itens();

			$data = json_decode($data, true);

			if (isset($data['id_pedido']) && !empty($data['id_pedido']) && is_numeric($data['id_pedido'])) {

				$id_pedido = addslashes($data['id_pedido']);

				header("Content-Type: application/json");
				echo json_encode($itens->listarItens($id_pedido));

			}

		}

	}

	// Gera um novo pedido
	public function add() {

		$data = file_get_contents("php://input");

		if (!empty($data)) {

			$pedido = new Pedidos();
			$itens 	= new Itens();

			$data = json_decode($data, true);

			if (isset($data['cliente']) && !empty($data['cliente']) && is_numeric($data['cliente'])) {
				
				$id_cliente = addslashes($data['cliente']);

				$id_pedido = $pedido->addPedido($id_cliente);

				$itens_return = array();

				foreach ($data['itens'] as $item) {

					$id    		= addslashes($item['id']);
					$valor 		= isset($data['valor']) ? addslashes($valor['valor']) : "";
					$quantidade = addslashes($item['quantidade']);

					$itens->addItem($id_pedido, $id, $valor, $quantidade);

					$itens_return[] = $this->returnRentabilidade($itens, $id, $valor);

				}

				$pedido->updateTotal($id_pedido);

				$return = array(
					"id_pedido" => $id_pedido,
					"status" => "Aberto",
					"itens" => $itens_return
				);

				header("Content-Type: application/json");
				echo json_encode($return);

			}

		}

	}

	// Adiciona um item em uma venda
	public function addItem() {

		$data = file_get_contents("php://input");

		if (!empty($data)) {
			
			$pedido = new Pedidos();
			$itens 	= new Itens();

			$data = json_decode($data, true);

			if (isset($data['id_pedido']) && !empty($data['id_pedido']) && is_numeric($data['id_pedido'])) {
				
				$id_pedido = addslashes($data['id_pedido']);

				if ($pedido->verificaStatus($id_pedido) == 0) {

					foreach ($data['itens'] as $item) {
						
						$id    		= addslashes($item['id']);
						$valor 		= isset($data['valor']) ? addslashes($valor['valor']) : "";
						$quantidade = addslashes($item['quantidade']);

						$itens->addItem($id_pedido, $id, $valor, $quantidade);

						$itens_return[] = $this->returnRentabilidade($itens, $id, $valor);

					}

					$pedido->updateTotal($id_pedido);

					$return = array(
						"id_pedido" => $id_pedido,
						"status" => "Aberto",
						"itens" => $itens_return
					);

					header("Content-Type: application/json");
					echo json_encode($return);

				}
				else {
					header("Content-Type: application/json");
					echo json_encode(array(
						"id_pedido" => $id_pedido,
						"status" => "Encerrado",
						"itens" => ""
					));
				}

			}

		}

	}

	// Altera um item de uma venda
	public function alterarItem() {

		$data = file_get_contents("php://input");

		if (!empty($data)) {
			
			$pedido = new Pedidos();
			$itens = new Itens();

			$data = json_decode($data, true);

			if (isset($data['id_pedido']) && !empty($data['id_pedido']) && is_numeric($data['id_pedido'])) {

				$id_pedido = addslashes($data['id_pedido']);
				
				if ($pedido->verificaStatus($id_pedido) == 0) {
					
					foreach ($data['itens'] as $item) {

						$id 		= addslashes($item['id']);
						$valor 		= isset($data['valor']) ? addslashes($valor['valor']) : "";
						$quantidade = addslashes($item['quantidade']);

						$itens->updateItem($id_pedido, $id, $valor, $quantidade, 0, false);

						$itens_return[] = $this->returnRentabilidade($itens, $id, $valor);

					}

					$pedido->updateTotal($id_pedido);

					$return = array(
						"id_pedido" => $id_pedido,
						"status" => "Aberto",
						"itens" => $itens_return
					);

					header("Content-Type: application/json");
					echo json_encode($return);

				}
				else {
					header("Content-Type: application/json");
					echo json_encode(array(
						"id_pedido" => $id_pedido,
						"status" => "Encerrado",
						"itens" => ""
					));
				}

			}

		}

	}

	// Deleta um item de uma venda
	public function deleteItem() {

		$data = file_get_contents("php://input");

		if (!empty($data)) {
			
			$pedido = new Pedidos();
			$itens = new Itens();

			$data = json_decode($data, true);

			if (isset($data['id_pedido']) && !empty($data['id_pedido']) && is_numeric($data['id_pedido'])) {

				$id_pedido = addslashes($data['id_pedido']);
				
				if ($pedido->verificaStatus($id_pedido) == 0) {
					
					foreach ($data['itens'] as $item) {
						$id = addslashes($item['id']);
						$itens->deleteItem($id_pedido, $id);
					}

					$pedido->updateTotal($id_pedido);

				}

			}

		}

	}

	// Encerra uma venda
	public function encerrar() {

		$data = file_get_contents("php://input");

		if (!empty($data)) {
			
			$pedido = new Pedidos();

			$data = json_decode($data, true);

			if (isset($data['id_pedido']) && !empty($data['id_pedido']) && is_numeric($data['id_pedido'])) {
				$id_pedido = addslashes($data['id_pedido']);
				$pedido->encerrarPedido($id_pedido);
			}

			header("Content-Type: application/json");
			echo json_encode(array(
				"id_pedido" => $id_pedido,
				"status" => "Encerrado"
			));

		}

	}

	// Deleta um pedido
	public function delete() {

		$data = file_get_contents("php://input");

		if (!empty($data)) {

			$pedido = new Pedidos();
			$itens = new Itens();

			$data = json_decode($data, true);

			if (isset($data['id_pedido']) && !empty($data['id_pedido']) && is_numeric($data['id_pedido'])) {
				
				$id_pedido = addslashes($data['id_pedido']);

				$pedido->delete($id_pedido);
				$itens->deleteItens($id_pedido);

				header("Content-Type: application/json");
				echo json_encode(array(
					"id_pedido" => $id_pedido,
					"status" => "Excluido",
					"itens" => ""
				));

			}

		}

	}

	// Monta a mensagem de retorno da rentabilidade para cada item da venda
	public function returnRentabilidade($itens, $id, $valor) {

		$rentabilidade = $itens->calculaRentabilidade($id, $valor);

		if ($rentabilidade == 1) {
			$msg = "Rentabilidade Otima";
		}
		else if ($rentabilidade == 2){
			$msg = "Rentabilidade Boa";
		}
		else {
			$msg = "Rentabilidade Ruim";
		}

		return array(
			"id" => $id,
			"rentabilidade" => $msg
		);

	}

}