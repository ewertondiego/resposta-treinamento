<style>
    .filtro-panel {
        width: 100%;
        max-width: 500px;
        padding: 15px;
        margin: 0 auto;
        margin-top: 60px;
    }
    .vendas-lista {
        width: 100%;
        max-width: 800px;
        padding: 15px;
        margin: 0 auto;
        margin-top: 20px;
    }
</style>
<div class="container">
    <div class="filtro-panel panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title text-center">Filtrar Vendas</h1>
        </div>
        <div class="panel-body">
            <div id="alerta"></div>
            <div id="filtroForm">
                <div class="form-group">
                    <label for="data_inicial">Data Inicial</label>
                    <input class="form-control" id="data_inicial" type="date" required>
                </div>
                <div class="form-group">
                    <label for="data_final">Data Final</label>
                    <input class="form-control" id="data_final" type="date" required>
                </div>
                <button type="submit" class="btn btn-lg btn-primary btn-block" onclick="filtrarVendas()">Filtrar Vendas</button>
            </div>
        </div>
    </div>

    <div class="vendas-lista panel panel-default">
        <div class="panel-heading">
            <h1 class="panel-title text-center">Lista de Vendas</h1>
        </div>
        <div class="panel-body">
            <div id="vendas"></div>
        </div>
    </div>
</div>

<script>
    function exibirAviso(mensagem, idAlvo) {
        $("#" + idAlvo).html('<div class="alert alert-warning">' + mensagem + '</div>');
    }

    function filtrarVendas() {
        let data_inicial = $("#data_inicial").val();
        let data_final = $("#data_final").val();

        $.ajax({
            url: "ver_vendas.php", // URL para o script de listagem de vendas
            type: "POST",
            dataType: "json",
            data: {
                data_inicial: data_inicial,
                data_final: data_final
            },
            cache: false,
            success: function(data) {
                if (data.sucesso) {
                    let vendasHtml = '';
                    data.vendas.forEach(function(venda) {
                        vendasHtml += 'ID da Venda: ' + venda.id_venda + ' - Produto ID: ' + venda.id_produto + ' - Quantidade: ' + venda.quantidade + ' - Data: ' + venda.data_venda + '<br>';
                    });
                    $("#vendas").html(vendasHtml);
                } else {
                    exibirAviso('Erro ao filtrar vendas: ' + data.mensagem, 'alerta');
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
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];
    $id_loja = 1; // Substituir pelo ID real da loja logada

    $sql = "SELECT * FROM venda WHERE id_loja = $id_loja AND data_venda BETWEEN '$data_inicial' AND '$data_final'";
    $resultado = $conn->query($sql);

    $response = array();
    if ($resultado->num_rows > 0) {
        $response['sucesso'] = true;
        $response['vendas'] = array();
        while ($row = $resultado->fetch_assoc()) {
            $response['vendas'][] = $row;
        }
    } else {
        $response['sucesso'] = false;
        $response['mensagem'] = 'Nenhuma venda encontrada no período selecionado.';
    }
    echo json_encode($response);
    exit;
}

$conn->close();
?>