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
            <h1 class="panel-title text-center">Editar Produto</h1>
        </div>
        <div class="panel-body">
            <div id="alerta"></div>
            <div id="editarForm">
                <input type="hidden" id="id_produto">
                <label for="id_produto">ID Produto</label>
                    <input class="form-control" placeholder="Id_produto" id="id_produto" type="number" required>
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
                <div class="form-group">
                    <label for="descricao">Descricao</label>
                    <input class="form-control" placeholder="Descricao" id="descricao"  maxlength="255" required autofocus>
                </div>
                <button type="submit" class="btn btn-lg btn-success btn-block" onclick="editarProduto()">Salvar Alterações</button>
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
            exibirAviso('Descricao inválida', 'alerta');
            return false;
        }
        return true;
    }

    function carregarProduto(id_produto) {
        $.ajax({
            url: "carregar_produto.php", // URL para carregar os dados do produto
            type: "GET",
            dataType: "json",
            data: { id: id_produto },
            cache: false,
            success: function(data) {
                if (data.sucesso) {
                    $("#id_produto").val(data.produto.id_produto);
                    $("#nome").val(data.produto.nome);
                    $("#custo").val(data.produto.custo);
                    $("#preco").val(data.produto.preco);
                    $("#estoque").val(data.produto.estoque);
                    $("#descricao").val(data.produto.descricao);
                } else {
                    exibirAviso('Erro ao carregar produto: ' + data.mensagem, 'alerta');
                }
            },
            error: function() {
                exibirAviso('Aconteceu um erro em nosso servidor', 'alerta');
            }
        });
    }

    function editarProduto() {
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
            url: "editar_produto.php", // URL para editar o produto
            type: "POST",
            dataType: "json",
            data: {
                id_produto: id_produto,
                nome: nome,
                preco: preco,
                estoque: estoque,
                descricao: descricao
            },
            cache: false,
            success: function(data) {
                if (data.sucesso) {
                    exibirAviso('Produto atualizado com sucesso!', 'alerta');
                } else {
                    exibirAviso('Erro ao atualizar produto: ' + data.mensagem, 'alerta');
                }
            },
            error: function() {
                exibirAviso('Aconteceu um erro em nosso servidor', 'alerta');
            }
        });
    }

    $(document).ready(function() {
        let urlParams = new URLSearchParams(window.location.search);
        let id_produto = urlParams.get('id');
        if (id_produto) {
            carregarProduto(id_produto);
        } else {
            exibirAviso('ID do produto não especificado.', 'alerta');
        }
    });
</script>