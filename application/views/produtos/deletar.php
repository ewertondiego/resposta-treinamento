<style>
    .cadastro-panel {
        width: 100%;
        max-width: 500px;
        padding: 15px;
        margin: 0 auto;
        margin-top: 60px;
    }
</style>
<div class="container">
    <div class="cadastro-panel panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title text-center">Deletar Produto</h1>
        </div>
        <div class="panel-body">
            <div id="alerta"></div>
            <div id="deletarForm">
                <div class="form-group">
                    <label for="id_produto">ID do Produto</label>
                    <input class="form-control" placeholder="ID do Produto" id="id_produto" type="number" required autofocus>
                </div>
                <button type="submit" class="btn btn-lg btn-danger btn-block" onclick="deletarProduto()">Deletar Produto</button>
            </div>
        </div>
    </div>
</div>

<script>
    function exibirAviso(mensagem, idAlvo) {
        $("#" + idAlvo).html('<div class="alert alert-warning">' + mensagem + '</div>');
    }

    function deletarProduto() {
        let id_produto = $("#id_produto").val();

        $.ajax({
            url: "deletar_produto.php", // URL para o script de deleção de produtos
            type: "POST",
            dataType: "json",
            data: {
                id_produto: id_produto
            },
            cache: false,
            success: function(data) {
                if (data.sucesso) {
                    exibirAviso('Produto deletado com sucesso!', 'alerta');
                } else {
                    exibirAviso('Erro ao deletar produto: ' + data.mensagem, 'alerta');
                }
            },
            error: function() {
                exibirAviso('Aconteceu um erro em nosso servidor', 'alerta');
            }
        });
    }
</script>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "treinamento";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produto = $_POST['id_produto'];

    // Verificar se o produto já foi comprado
    $sql_verificar = "SELECT COUNT(*) AS total FROM venda WHERE id_produto = $id_produto";
    $result_verificar = $conn->query($sql_verificar);
    $row_verificar = $result_verificar->fetch_assoc();

    $response = array();
    if ($row_verificar['total'] > 0) {
        $response['sucesso'] = false;
        $response['mensagem'] = 'Produto não pode ser deletado pois já foi comprado.';
    } else {
        $sql_deletar = "DELETE FROM produtos WHERE id = $id_produto";
        if ($conn->query($sql_deletar) === TRUE) {
            $response['sucesso'] = true;
        } else {
            $response['sucesso'] = false;
            $response['mensagem'] = $conn->error;
        }
    }
    echo json_encode($response);
    exit;
}

$conn->close();
?>