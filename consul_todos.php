<?php
include("conexao.php");

$sql_consulta = "SELECT * FROM produtos ORDER BY nomeprod";
$resultado = $conexao->query($sql_consulta);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos os Produtos Cadastrados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col">
                <h2 class="mt-3">Produtos Cadastrados</h2>
                <a href="inicial.php" class="btn btn-secondary mb-3">Voltar ao Início</a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col">
                <table class="table table-hover table-striped">
                    <thead style="background-color: #0d6efd; color: white; border-color: #0a58ca;">
                        <tr>
                            <th style="color: white !important; background-color: #0d6efd;">Código (ID)</th>
                            <th style="color: white !important; background-color: #0d6efd;">Nome Produto</th>
                            <th style="color: white !important; background-color: #0d6efd;">Categoria</th>
                            <th style="color: white !important; background-color: #0d6efd;">Valor (R$)</th>
                            <th style="color: white !important; background-color: #0d6efd;">Imagem</th>
                            <th style="color: white !important; background-color: #0d6efd;">Especificações</th>
                            <th style="color: white !important; background-color: #0d6efd;">Editar</th>
                            <th style="color: white !important; background-color: #0d6efd;">Excluir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultado->num_rows > 0) {
                            while ($linha = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $linha["id_prod"] . "</td>";
                                echo "<td>" . $linha["nomeprod"] . "</td>";
                                echo "<td>" . $linha["categorias"] . "</td>";
                                echo "<td> R$ " . number_format($linha["preco"], 2, ',', '.') . "</td>";
                                echo "<td>" . $linha["img"] . "</td>";
                                echo "<td>" . $linha["especificacoes"] . "</td>";
                                echo "<td><a href='editar.php?id=" . $linha["id_prod"] . "' class='btn btn-warning btn-sm'>Editar</a></td>";
                                echo "<td>
                                        <form action='excluir.php' method='POST' style='display:inline;'>
                                            <input type='hidden' name='id' value='" . $linha["id_prod"] . "'>
                                            <button type='submit' class='btn btn-danger btn-sm' onclick=\"return confirm('Tem certeza?');\">Excluir</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Nenhum produto cadastrado.</td></tr>";
                        }
                        $conexao->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>