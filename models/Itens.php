<?php

class Itens extends Model {

	// Lista os itens de um determinado pedido
	public function listarItens($id_pedido) {

		$sql = " SELECT pi.id_produto,
						p.nome,
						pi.valor,
						pi.quantidade,
						CASE
							WHEN pi.rentabilidade = 1 THEN 'Rentabilidade Otima'
							ELSE 'Rentabilidade Boa'
						END AS rentabilidade
				   FROM pedidos_item pi
				   JOIN pedidos pe ON pe.id = pi.id_pedido
				   LEFT JOIN produtos p ON pi.id_produto = p.id
				  WHERE pi.id_pedido = :id_pedido
				  ORDER BY pi.id";

		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id_pedido", $id_pedido);

		$stmt->execute();

		return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : array();

	}

	public function addItem($id_pedido, $id, $valor, $quantidade) {

		if (!isset($valor) || empty($valor) || $valor == "" || $valor <= 0) {
			$valor = $this->getPrecoUnit($id);
		}

		if ($quantidade > 0) {

			$multiplo = $this->getMultiplo($id);

			if ($quantidade % $multiplo == 0) {
				
				$rentabilidade = $this->calculaRentabilidade($id, $valor);

				if ($rentabilidade != 3) {

					if ($this->verificaItem($id_pedido, $id) == 0) {

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

						} catch (PDOException $e) {
							echo "Falhou: " . $e->getMessage();
						}

					}
					else {
						$this->updateItem($id_pedido, $id, $valor, $quantidade, $rentabilidade, true);
					}

				}

			}

		}

	}

	// Altera um item
	public function updateItem($id_pedido, $id, $valor, $quantidade, $rentabilidade, $incremental) {

		// Incrementa a quantidade
		if ($incremental) {
			$inc = "quantidade + :quantidade";
		}
		else {
			$inc = ":quantidade";
		}

		if ($rentabilidade == 0) {
			$rentabilidade = $this->calculaRentabilidade($id, $valor);
		}

		if (!isset($valor) || empty($valor) || $valor == "" || $valor <= 0) {
			$valor = $this->getPrecoUnit($id);
		}

		if ($rentabilidade != 3) {
			
			$sql = " UPDATE pedidos_item
						SET valor = :valor,
							quantidade = $inc,
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

	}

	// Remove um determinado item de uma venda
	public function deleteItem($id_pedido, $id) {

		$sql  = "DELETE FROM pedidos_item WHERE id_produto = :id_produto AND id_pedido = :id_pedido";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id_pedido", $id_pedido);
		$stmt->bindValue(":id_produto", $id);

		$stmt->execute();

	}

	// Deleta todos os itens de uma venda
	public function deleteItens($id_pedido) {

		$sql  = "DELETE FROM pedidos_item WHERE id_pedido = :id_pedido";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id_pedido", $id_pedido);

		$stmt->execute();

	}

	// Verifica se um item já se encontra em uma venda
	public function verificaItem($id_pedido, $id) {

		$sql = "SELECT COUNT(*) AS item FROM pedidos_item WHERE id_produto = :id_produto AND id_pedido = :id_pedido";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id_pedido", $id_pedido);
		$stmt->bindValue(":id_produto", $id);

		$stmt->execute();

		$item = $stmt->fetch();
		$item = $item['item'];

		return $item;

	}

	// Calcula a rentabilidade de um produto
	public function calculaRentabilidade($id, $preco_venda) {

		if (!isset($preco_venda) || empty($preco_venda) || $preco_venda == "" || $preco_venda <= 0) {
			$preco_venda = $this->getPrecoUnit($id);
		}
		else {
			$preco_venda = number_format($preco_venda, 2, '.', '');
		}
		
		$preco_orig = $this->getPrecoUnit($id);
		$desconto = $preco_orig * 0.90;

		if ($preco_venda > $preco_orig) {
			return 1; // Rentabilidade ótima
		}
		else if ($preco_venda >= $desconto || $preco_venda == $preco_orig) {
			return 2; // Rentabilidade boa
		}
		else {
			return 3; // Rentabilidade ruim
		}

	}

	// Pega o preço unitário de um produto caso o usuário não informe um valor
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

	// Pega o múltiplo cadastrado no produto
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