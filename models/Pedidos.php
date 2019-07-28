<?php

class Pedidos extends Model {

	public function listAll() {

		$sql = " SELECT p.id,
						p.data,
						CASE 
							WHEN p.status = 0 THEN 'Aberto'
							ELSE 'Encerrado'
						END status,
						c.nome,
						p.total
				   FROM pedidos p
				   LEFT JOIN clientes c ON p.id_cliente = c.id
				  ORDER BY data DESC";

		$sql = $this->pdo->query($sql);

		return $sql->rowCount() > 0 ? $sql->fetchAll(PDO::FETCH_ASSOC) : array();

	}

	public function addPedido($id_cliente) {

		try {

			$sql  = "INSERT INTO pedidos (id_cliente) VALUES (:id_cliente)";
			$stmt = $this->pdo->prepare($sql);

			$stmt->bindValue(":id_cliente", $id_cliente);

			$stmt->execute();

			return $this->pdo->lastInsertId();

		} catch(PDOException $e) {
			echo "Falhou: " . $e->getMessage();
		}

		return 0;

	}

	public function updateTotal($id_pedido) {

		$sql  = "SELECT SUM(valor * quantidade) AS total FROM pedidos_item WHERE id_pedido = :id_pedido";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id_pedido", $id_pedido);

		$stmt->execute();

		if ($stmt->rowCount() > 0) {
			
			$total = $stmt->fetch();
			$total = number_format($total['total'], 2, '.', '');

			$sql  = "UPDATE pedidos SET total = :total WHERE id = :id";
			$stmt = $this->pdo->prepare($sql);

			$stmt->bindValue(":total", $total);
			$stmt->bindValue(":id", $id_pedido);

			$stmt->execute();

		}

	}

	public function delete($id) {

		$sql  = "DELETE FROM pedidos WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id", $id);

		$stmt->execute();

	}

	public function verificaStatus($id) {

		$sql  = "SELECT status FROM pedidos WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);

		$stmt->bindValue(":id", $id);

		$stmt->execute();

		if ($stmt->rowCount() > 0) {
			
			$status = $stmt->fetch();
			$status = $status['status'];

			return $status;

		}

	}

}