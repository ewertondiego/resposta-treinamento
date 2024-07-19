<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "treinamento";

$conn = new mysqli($servername, $username, $password, $dbname);

// Função para carregar detalhes dos produtos
function carregarProduto($conn, $id_produto) {
    $sql = "SELECT * FROM produto WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('id', $id_produto);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Atualizar a quantidade do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    if ($_POST['acao'] === 'atualizar') {
        $id_produto = intval($_POST['id_produto']);
        $quantidade = intval($_POST['quantidade']);
        if ($quantidade > 0) {
            $_SESSION['carrinho'][$id_produto] = $quantidade;
        } else {
            unset($_SESSION['carrinho'][$id_produto]);
        }
    } elseif ($_POST['acao'] === 'remover') {
        $id_produto = intval($_POST['id_produto']);
        unset($_SESSION['carrinho'][$id_produto]);
    } elseif ($_POST['acao'] === 'confirmar') {
        // Processo de confirmação de compra
        $id_usuario = $_SESSION['id_usuario'] ?? 0;
        $conn->begin_transaction();

        try {
            $sql = "INSERT INTO vendas (id_usuario, data_venda) VALUES (?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('id', $id_usuario);
            $stmt->execute();
            $id_venda = $conn->insert_id;

            foreach ($_SESSION['carrinho'] as $id_produto => $quantidade) {
                $sql = "INSERT INTO itens_venda (id_venda, id_produto, quantidade) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('iii', $id_venda, $id_produto, $quantidade);
                $stmt->execute();
            }

            $_SESSION['carrinho'] = array();
            $conn->commit();
            echo "<script>alert('Compra confirmada com sucesso!'); window.location.href='produtos.php';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Erro ao confirmar a compra: " . $e->getMessage() . "');</script>";
        }
        exit;
    }
}

$total_itens = array_sum($_SESSION['carrinho'] ?? []);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .carrinho-panel {
            width: 100%;
            max-width: 800px;
            padding: 15px;
            margin: 0 auto;
            margin-top: 60px;
        }
        .item-carrinho {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="carrinho-panel panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title text-center">Carrinho de Compras</h1>
            </div>
            <div class="panel-body">
                <?php if (empty($_SESSION['carrinho'])): ?>
                    <p>Seu carrinho está vazio.</p>
                <?php else: ?>
                    <form method="post" action="carrinho.php">
                        <?php foreach ($_SESSION['carrinho'] as $id_produto => $quantidade): ?>
                            <?php $produto = carregarProduto($conn, $id_produto); ?>
                            <div class="carrinho_item">
                                <h2><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h2>
                                <p>Preço: R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                                <p>Estoque: <?= htmlspecialchars($produto['estoque'], ENT_QUOTES, 'UTF-8') ?></p>
                                <input type="hidden" name="id_produto" value="<?= $id_produto ?>">
                                <input type="number" name="quantidade" value="<?= $quantidade ?>" min="1" max="<?= htmlspecialchars($produto['estoque'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" name="acao" value="atualizar" class="btn btn-primary">Atualizar Quantidade</button>
                                <button type="submit" name="acao" value="remover" class="btn btn-danger">Remover</button>
                            </div>
                        <?php endforeach; ?>
                        <button type="submit" name="acao" value="confirmar" class="btn btn-success">Confirmar Compra</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
