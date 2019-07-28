<?php

class Itens extends Model {

	public function addItem($id_pedido, $id, $valor, $quantidade) {

		if (empty($valor) || $valor <= 0) {
			$valor = $this->getPrecoUnit($id);
		}

		if ($quantidade > 0) {

			$multiplo = $this->getMultiplo($id);

			if ($quantidade % $multiplo == 0) {
				
				$rentabilidade = $this->calculaRentabilidade($id, $valor);

				if ($rentabilidade != 3) {

					if ($this->verificaItem($id_pedido, $id)) {

						try {
							
							$sql = "INSERT INTO pedidos_item ( id_pedido, 
															   id_produto,
															   valor,
															   quantidade,
															   rentabilidade
															 )
													  VALUES ( :id_pedido,
													  		   :id_produto,
													  		   :valor,
													  		   :quantidade,
													  		   :rentabilidade
															 )";

							$stmt = $this->pdo->prepare($sql);

							$stmt->bindValue(":id_pedido", $id_pedido);
							$stmt->bindValue(":id_produto", $id);
							$stmt->bindValue(":valor", $valor);
							$stmt->bindValue(":quantidade", $quantidade);
							$stmt->bindValue(":rentabilidade", $rentabilidade);

							$stmt->execute();

						} catch (Exception $e) {
							echo "Falhou: " . $e->getMessage();
						}

					}
					else {
						$this->updateItem($id_pedido, $id, $valor, $quantidade, $rentabilidade);
					}

				}

			}

		}

	}

	public function updateItem($id_pedido, $id, $valor, $quantidade, $rentabilidade) {

		$sql = " UPDATE pedidos_item
					SET valor = :valor,
						quantidade = quantidade + :quantidade,
						rentabilidade = :rentabilidade
				  WHERE id_produto = :id_produto
				  	AND id_pedido = :id_pedido";

		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id_pedido", $id_pedido);
		$stmt->bindValue(":id_produto", $id);
		$stmt->bindValue(":valor", $valor);
		$stmt->bindValue(":quantidade", $quantidade);
		$stmt->bindValue(":rentabilidade", $rentabilidade);

		$stmt->execute();

	}

	public function deleteItem($id_pedido, $id) {

		$sql  = "DELETE FROM pedidos_item WHERE id_produto = :id_produto AND id_pedido = :id_pedido";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id_pedido", $id_pedido);
		$stmt->bindValue(":id_produto", $id);

		$stmt->execute();

	}

	public function deleteItens($id_pedido) {

		$sql  = "DELETE FROM pedidos_item WHERE id_pedido = :id_pedido";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id_pedido", $id_pedido);

		$stmt->execute();

	}

	public function verificaItem($id_pedido, $id) {

		$sql = "SELECT COUNT(*) AS item FROM pedidos_item WHERE id_produto = :id_produto AND id_pedido = :id_pedido";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id_pedido", $id_pedido);
		$stmt->bindValue(":id_produto", $id);

		$stmt->execute();

		return $stmt->rowCount() == 0;

	}

	public function calculaRentabilidade($id, $preco_venda) {

		$preco_venda = number_format($preco_venda, 2, '.', '');
		$preco_orig = $this->getPrecoUnit($id);
		$desconto = $preco_orig * 0.90;

		if ($preco_venda > $preco_orig) {
			return 1; // Rentabilidade ótima
		}
		else if ($preco_venda >= $desconto) {
			return 2; // Rentabilidade boa
		}
		else {
			return 3; // Rentabilidade ruim
		}

	}

	public function getPrecoUnit($id) {

		$sql  = "SELECT preco_unit FROM produtos WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id", $id);

		$stmt->execute();

		if ($stmt->rowCount() > 0) {

			$result = $stmt->fetch();
			$preco_orig = number_format($result['preco_unit'], 2, '.', '');

			return $preco_orig;

		}

		return 0;

	}

	public function getMultiplo($id) {

		$sql  = "SELECT COALESCE(multiplo, 1) AS multiplo FROM produtos WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id", $id);

		$stmt->execute();

		if ($stmt->rowCount() > 0) {

			$multiplo = $stmt->fetch();
			$multiplo = $multiplo['multiplo'];

			return $multiplo;

		}

		return 1;

	}

}