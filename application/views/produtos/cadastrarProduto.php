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
            <h1 class="panel-title text-center">Cadastro de Produto</h1>
        </div>
        <div class="panel-body">
            <div id="alerta"></div>
            <div id="cadastroForm">
            <div class="form-group">
                    <label for="id_produto">ID Produto</label>
                    <input class="form-control" placeholder="Id_produto" id="id_produto" type="text" type="number" required>
                </div>
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input class="form-control" placeholder="Nome" id="nome" type="text" maxlength="255" required autofocus>
                </div>
                <div class="form-group">
                    <label for="custo">Custo</label>
                    <input class="form-control" placeholder="Custo" id="custo" type="number" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="preco">Preço</label>
                    <input class="form-control" placeholder="Preço" id="preco" type="number" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="estoque">Estoque</label>
                    <input class="form-control" placeholder="Estoque" id="estoque" type="number" required>
                </div>
                </div>
                <div class="form-group">
                    <label for="descricao">Descrição</label>
                    <input class="form-control" placeholder="Descricao" id="descricao"  maxlength="255" required autofocus>
                </div>
                <button type="submit" class="btn btn-lg btn-success btn-block" onclick="cadastrarProduto()">Cadastrar Produto</button>
            </div>
        </div>
    </div>
</div>

<script>
    function exibirAviso(mensagem, idAlvo) {
        $("#" + idAlvo).html('<div class="alert alert-warning">' + mensagem + '</div>');
    }


    function validarDadosProduto() {
        let id_produto = $("#id_produto").val();
        let nome = $("#nome").val();
        let custo = $("#custo").val();
        let preco = $("#preco").val();
        let estoque = $("#estoque").val();
        let descricao = $("#descricao").val();


        if (!id_produto || id_produto <= 0) {
            exibirAviso('Id do Produto inválido', 'alerta');
            return false;
        }
        if (!nome) {
            exibirAviso('Nome inválido', 'alerta');
            return false;
        }
        if (!custo) {
            exibirAviso('Custo inválido', 'alerta');
            return false;
        }
        if (!preco || preco <= 0) {
            exibirAviso('Preço inválido', 'alerta');
            return false;
        }
        if (!estoque || estoque < 0) {
            exibirAviso('Estoque inválido', 'alerta');
            return false;
        }
        if (!descricao || descricao < 0) {
            exibirAviso('Descriçao inválido', 'alerta');
            return false;
        }
        return true;
    }


    function cadastrarProduto() {
        if (!validarDadosProduto()) {
            return;
        }


        let id_produto = $("#id_produto").val();
        let nome = $("#nome").val();
        let custo = $("#custo").val();
        let preco = $("#preco").val();
        let estoque = $("#estoque").val();
        let descricao = $("#descricao").val();


        $.ajax({
            url: "cadastro_produto.php", // URL para o script de cadastro de produtos
            type: "POST",
            dataType: "json",
            data: {
                id_produto: id_produto,
                nome: nome,
                custo: custo,
                preco: preco,
                estoque: estoque,
                descricao:descricao
            },
            cache: false,
            success: function(data) {
                if (data.sucesso) {
                    exibirAviso('Produto cadastrado com sucesso!', 'alerta');
                } else {
                    exibirAviso('Erro ao cadastrar produto: ' + data.mensagem, 'alerta');
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
    $id_produto= $_POST['id_produto'];
    $nome = $_POST['nome'];
    $custo = $_POST['custo'];
    $preco = $_POST['preco'];
    $estoque = $_POST['estoque'];
    $descricao = $_POST['descricao'];
    $id_produto = 1; // Substituir pelo ID real da loja logada


    $sql = "INSERT INTO produto (id_produto, nome, custo, descricao, preco, estoque) VALUES ('$id_produto', '$nome', $custo, '$preco', '$estoque', '$descricao')";

    $response = array();
    if ($conn->query($sql) === TRUE) {
        $response['sucesso'] = true;
    } else {
        $response['sucesso'] = false;
        $response['mensagem'] = $conn->error;
    }
    echo json_encode($response);
    exit;
}


$conn->close();
?>