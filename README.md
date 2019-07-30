# WebService

## Webservice para controle simples de vendas

O header de envio deve ser no formato "Content-Type: application/json".
O body deve ser um json, conforme regras de cada endpoint.

# Rentabilidade

## Ótima

Quando o preço usado no pedido é maior que o preço do produto. Ex: se o preço do produto é de R$ 100, a rentabilidade será ótima se oitem for vendido por R$ 100,01 (inclusive) ou mais.

## Boa

Quando o preço do item é no máximo 10% menor que o preço do produto. Ex: se o preço do produto é de R$ 100, a rentabilidade será boa se o item for vendido por qualquer preço entre R$ 90 (inclusive) e R$ 100 (inclusive).

## Ruim

Quando o preço do item é inferior ao preço do produto menos 10%. Ex: se o preço do produto é de R$ 100, a rentabilidade será ruim se o preço for menor ou igual a R$ 89,99. (Itens com essa rentabilidade não são incluídos no pedido)

# Endpoints

## Clientes

url: https://backendcaiotest.000webhostapp.com/clientes -> Lista todos os clientes (GET, sem parâmetros)

## Produtos

url: https://backendcaiotest.000webhostapp.com/produtos -> Lista todos os produtos (GET, sem parâmetros)

## Pedidos

url: https://backendcaiotest.000webhostapp.com/pedidos/listar -> Lista todos os pedidos feitos (GET, sem parâmetros)

url: https://backendcaiotest.000webhostapp.com/pedidos/add -> Adiciona um item no pedido (POST):

### Envio

{
	"cliente": 1,
	"itens": [
		{
			"id": 1,
			"valor": 550000,
			"quantidade": 2
		},
		{
			"id": 2,
			"valor": 60000,
			"quantidade": 2
		}
	]
}

### Retorno

{
	"id_pedido": "1",
	"status": "Aberto",
	"itens": [
		{
			"id": "2",
			"rentabilidade": "Rentabilidade Boa"
		},
		{
			"id": "2",
			"rentabilidade": "Rentabilidade Boa"
		}
	],
}

url: https://backendcaiotest.000webhostapp.com/pedidos/itens -> Lista todos os itens de um pedido (POST):

### Envio

{
	"id_pedido": 1
}

### Retorno

{
	"id_produto": 2,
	"nome": "X-Wing",
	"valor": "60000.00",
	"quantidade": 2,
	"rentabilidade": "Rentabilidade Boa"
}

url: https://backendcaiotest.000webhostapp.com/pedidos/addItem -> Adiciona um ou mais itens de um pedido, caso o item já esteja inserido o mesmo será alterado e incrementado com a quantidade enviada (POST):

### Envio

{
	"id_pedido": 1,
	"itens": [
		{
			"id": 1,
			"valor": 550000,
			"quantidade": 2
		},
		{
			"id": 2,
			"valor": 60000,
			"quantidade": 2
		}
	]
}

### Retorno

{
	"id_pedido": 1,
	"status": "Aberto",
	"itens": [
		{
			"id": "2",
			"rentabilidade": "Rentabilidade Boa"
		},
		{
			"id": "2",
			"rentabilidade": "Rentabilidade Boa"
		}
	]
}

url: https://backendcaiotest.000webhostapp.com/pedidos/alterarItem -> Altera um ou mais itens de um pedido (POST):

### Envio

{
	"id_pedido": 1,
	"itens": [
		{
			"id": 1,
			"valor": 550000,
			"quantidade": 2
		},
		{
			"id": 2,
			"valor": 60000,
			"quantidade": 2
		}
	]
}

### Retorno

{
	"id_pedido": 1,
	"status": "Aberto",
	"itens": [
		{
			"id": "2",
			"rentabilidade": "Rentabilidade Boa"
		},
		{
			"id": "2",
			"rentabilidade": "Rentabilidade Boa"
		}
	]
}

url: https://backendcaiotest.000webhostapp.com/pedidos/deleteItem -> Deleta um ou mais itens de um pedido (POST):

### Envio

{
	"id_pedido": 1,
	"itens": [
		{
			"id": 1
		},
		{
			"id": 2
		}
	]
}

### Retorno

Sem retorno

url: https://backendcaiotest.000webhostapp.com/pedidos/encerrar -> Encerra um pedido (POST):

### Envio

{
	"id_pedido": 1,
}

### Retorno

{
	"id_pedido": 1,
	"status": "Encerrado"
}

url: https://backendcaiotest.000webhostapp.com/pedidos/delete -> Deleta um pedido (POST):

### Envio

{
	"id_pedido": 1,
}

### Retorno

{
	"id_pedido": 1,
	"status": "Excluido",
	"itens": ""
}
