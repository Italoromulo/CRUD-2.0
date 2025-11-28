<?php
include("conexao.php");

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "SELECT * FROM produtos WHERE id_prod = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $produto = $resultado->fetch_assoc();
    } else {
        echo "<script>alert('Produto não encontrado.'); window.location.href = 'consul_todos.php';</script>";
        exit;
    }
    $stmt->close();
} else {
    echo "<script>alert('ID não informado.'); window.location.href = 'consul_todos.php';</script>";
    exit;
}
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        h2 {
            color: #ffffff;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }

        .card {
            background-color: #1e1e1e;
            border: 1px solid #444;
        }

        .form-control {
            background-color: #2b2b2b;
            color: #ffffff;
            border: 1px solid #555;
        }

        .form-control:focus {
            background-color: #333;
            color: #ffffff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-floating>label {
            color: #aaa;
        }

        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            color: #0d6efd;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }

        .btn-secondary {
            background-color: #4a67ff;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="mt-3">Editar Produto</h2>

                <form method="post" action="atualizar.php" enctype="multipart/form-data">

                    <input type="hidden" name="id_prod" value="<?php echo $produto['id_prod']; ?>">

                    <input type="hidden" name="img_atual_completa" value="<?php echo $produto['img']; ?>">

                    <div class="mt-3 form-floating">
                        <input type="text" class="form-control" id="nomeprod" name="nomeprod"
                            value="<?php echo htmlspecialchars($produto['nomeprod']); ?>" required>
                        <label for="nomeprod">Nome do produto</label>
                    </div>

                    <div class="mt-3 form-floating">
                        <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0"
                            value="<?php echo $produto['preco']; ?>" required>
                        <label for="preco">Valor (R$)</label>
                    </div>

                    <div class="mt-3 form-floating">
                        <input type="text" class="form-control" id="categorias" name="categorias"
                            value="<?php echo htmlspecialchars($produto['categorias']); ?>" required>
                        <label for="categorias">Categoria</label>
                    </div>

                    <div class="mt-3 p-3"
                        style="border: 1px solid #555; border-radius: 5px; background-color: #1e1e1e;">
                        <h5 class="text-white mb-3" style="border-bottom: 1px solid #444; padding-bottom: 5px;">
                            Gerenciar Arquivo de Imagem</h5>

                        <div class="mb-3">
                            <span class="text-white-50">Arquivo atual no sistema:</span><br>
                            <strong class="text-primary fs-5">
                                <?php echo !empty($produto['img']) ? htmlspecialchars($produto['img']) : "Nenhum arquivo"; ?>
                            </strong>
                        </div>

                        <?php
                        $nome_sem_extensao = !empty($produto['img']) ? pathinfo($produto['img'], PATHINFO_FILENAME) : "";
                        ?>
                        <div class="mb-3">
                            <label for="nome_arquivo" class="form-label text-white">Renomear arquivo (Opcional):</label>
                            <input type="text" class="form-control" id="nome_arquivo" name="nome_arquivo"
                                value="<?php echo htmlspecialchars($nome_sem_extensao); ?>"
                                placeholder="Digite o novo nome sem .jpg">
                            <div class="form-text text-white-50">Alterar este campo mudará o nome do arquivo na pasta e
                                no banco.</div>
                        </div>

                        <div>
                            <label for="arquivo" class="form-label text-white">Enviar nova imagem (Opcional):</label>
                            <input type="file" class="form-control" id="arquivo" name="arquivo" accept="image/*">
                        </div>
                    </div>

                    <div class="mt-3 form-floating">
                        <input type="text" class="form-control" id="especificacoes" name="especificacoes"
                            value="<?php echo htmlspecialchars($produto['especificacoes']); ?>">
                        <label for="especificacoes">Especificações</label>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary form-control">Salvar Alterações</button>
                    </div>
                </form>

                <a href="inicial.php" class="btn btn-secondary mt-3">Cancelar e Voltar</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
