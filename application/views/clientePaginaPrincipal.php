<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "treinamento";

$conn = new mysqli($servername, $username, $password, $dbname);

// Função para carregar produtos
function carregarProdutos($conn) {
    $sql = "SELECT * FROM produto";
    $resultado = $conn->query($sql);

    $produtos = array();
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $produtos[] = $row;
        }
    }
    return $produtos;
}

// Função para adicionar ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produto'])) {
    $id_produto = $_POST['id_produto'];
    $quantidade = $_POST['quantidade'];

    $sql = "SELECT estoque FROM produtos WHERE id = $id_produto";
    $resultado = $conn->query($sql);
    $row = $resultado->fetch_assoc();

    if ($row['estoque'] >= $quantidade) {
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = array();
        }

        if (isset($_SESSION['carrinho'][$id_produto])) {
            $_SESSION['carrinho'][$id_produto] += $quantidade;
        } else {
            $_SESSION['carrinho'][$id_produto] = $quantidade;
        }

        $total_itens = array_sum($_SESSION['carrinho']);
        $response = array('sucesso' => true, 'total_itens' => $total_itens);
    } else {
        $response = array('sucesso' => false, 'mensagem' => 'Quantidade solicitada maior que o estoque disponível');
    }
    echo json_encode($response);
    exit;
}

$produtos = carregarProdutos($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .produto-panel {
            width: 100%;
            max-width: 800px;
            padding: 15px;
            margin: 0 auto;
            margin-top: 60px;
        }
        .produto {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
        }
        .carrinho-icon {
            position: fixed;
            top: 10px;
            right: 10px;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="produto-panel panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title text-center">Produtos à Venda</h1>
            </div>
            <div class="panel-body" id="produtos">
                <?php foreach ($produtos as $produto): ?>
                    <div class="produto">
                        <h2><?= $produto['nome'] ?></h2>
                        <p>Preço: R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                        <p>Estoque: <?= $produto['estoque'] ?></p>
                        <input type="number" id="quantidade_<?= $produto['id'] ?>" placeholder="Quantidade" min="1" max="<?= $produto['estoque'] ?>">
                        <button class="btn btn-success" onclick="adicionarAoCarrinho(<?= $produto['id'] ?>)">Adicionar ao Carrinho</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="carrinho-icon">
            <i class="fa fa-shopping-cart"></i> <span id="carrinho-count"><?= array_sum($_SESSION['carrinho'] ?? []) ?></span>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function adicionarAoCarrinho(id_produto) {
            let quantidade = $(`#quantidade_${id_produto}`).val();
            if (quantidade <= 0) {
                alert('Quantidade inválida');
                return;
            }

            $.ajax({
                url: "produtos.php",
                type: "POST",
                dataType: "json",
                data: {
                    id_produto: id_produto,
                    quantidade: quantidade
                },
                cache: false,
                success: function(data) {
                    if (data.sucesso) {
                        $("#carrinho-count").text(data.total_itens);
                        alert('Produto adicionado ao carrinho');
                    } else {
                        alert('Erro ao adicionar produto ao carrinho: ' + data.mensagem);
                    }
                },
                error: function() {
                    alert('Erro no servidor');
                }
            });
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
