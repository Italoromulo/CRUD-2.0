<?php
include("conexao.php");

$resultado = null;
$nome_consulta = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_consulta = $_POST["nomeprod"];
    $sql = "SELECT * FROM produtos WHERE nomeprod LIKE ?";
    $stmt = $conexao->prepare($sql);
    $param_nome = "%" . $nome_consulta . "%";
    $stmt->bind_param("s", $param_nome);

    $stmt->execute();
    $resultado = $stmt->get_result();

}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        /* ===== TEMA DARK PARA O PROJETO DE PC ===== */

        /* --- TEMA GERAL --- */
        body {
            background-color: #121212;
            /* Fundo escuro */
            color: #e0e0e0;
            /* Texto claro */
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        /* --- TÍTULOS --- */
        h2,
        h3 {
            color: #ffffff;
            /* Azul primário do Bootstrap como destaque */
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }

        /* --- CARD (Tela Inicial) --- */
        .card {
            background-color: #1e1e1e;
            /* Fundo do card mais claro */
            border: 1px solid #444;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }

        .card-header {
            background-color: #0d6efd;
            color: white;
            font-size: 1.25rem;
            font-weight: bold;
            border-bottom: none;
            border-radius: 10px 10px 0 0 !important;
        }

        /* --- BOTÕES --- */
        /* Remove a cor de fundo padrão e adiciona transição */
        .btn {
            border: none;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
        }

        .btn:hover {
            transform: translateY(-2px);
            /* Efeito de "levantar" */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* Botões da tela inicial */
        .d-grid .btn-primary {
            background-color: #0d6efd;
        }

        .d-grid .btn-info {
            background-color: #315CFD;
            /* Tom de azul do seu 'style' antigo */
        }

        .d-grid .btn-secondary {
            background-color: #4a67ff;
            /* Tom de azul do seu 'style' antigo */
        }

        /* Botões da tabela (Editar/Excluir) */
        .btn-warning {
            color: #121212;
            /* Texto escuro para botão amarelo */
        }

        /* --- FORMULÁRIOS (Cadastro, Editar, Consulta) --- */
        .form-control {
            background-color: #2b2b2b;
            /* Fundo do input */
            color: #e0e0e0;
            /* Texto do input */
            border: 1px solid #555;
        }

        .form-control:focus {
            background-color: #333;
            color: #e0e0e0;
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Cor do texto do 'label' flutuante */
        .form-floating>label {
            color: #aaa;
        }

        /* Cor do 'label' quando o campo está focado ou preenchido */
        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            color: #0d6efd;
        }

        /* --- TABELA (Consultar Todos, Resultado Consulta) --- */
        .table {
            color: #e0e0e0;
            background-color: #1e1e1e;
            border-color: #444;
        }
        .table-light,
        .table-light>th,
        .table-light>td {
            background-color: #1e1e1e;
            color: #e0e0e0;
        }

        .table>thead {
            background-color: #0d6efd !important;
        }

        .table>thead th {
            color: white !important;
            /* Força o texto branco */
            border-color: #0a58ca;
        }

        /* Efeito de listra */
        .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: #2b2b2b;
            color: #e0e0e0;
        }

        /* Efeito de hover */
        .table-hover>tbody>tr:hover>* {
            background-color: #3c3c3c;
            color: #f0f0f0;
        }
        
    </style>

</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2 class="mt-4">Consultar Produto por Nome</h2>

                <form action="consulta.php" method="POST">
                    <div class="mt-3 form-floating">
                        <input type="text" class="form-control" id="nomeprod" name="nomeprod" placeholder="Nome do produto" value="<?php echo htmlspecialchars($nome_consulta); ?>" required>
                        <label for="nomeprod">Nome do Produto:</label>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary form-control">Consultar</button>
                </form>
                <br>
                <a href="inicial.php" class="btn btn-secondary form-control">Voltar ao Início</a>
            </div>
        </div>
        
        <?php
        if ($resultado !== null):
        ?>
        <hr class="mt-5">
        <div class="row mt-4">
            <div class="col">
                <h3>Resultados da Busca</h3>
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th style="background-color: #0d6efd;">ID</th>
                            <th style="background-color: #0d6efd;">Nome</th>
                            <th style="background-color: #0d6efd;">Categoria</th>
                            <th style="background-color: #0d6efd;">Preço (R$)</th>
                            <th style="background-color: #0d6efd;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultado->num_rows > 0) {
                            while($linha = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $linha["id_prod"] . "</td>";
                                echo "<td>" . $linha["nomeprod"] . "</td>";
                                echo "<td>" . $linha["categorias"] . "</td>";
                                echo "<td> R$ " . number_format($linha["preco"], 2, ',', '.') . "</td>";
                                echo "<td>
                                        <a href='editar.php?id=" . $linha["id_prod"] . "' class='btn btn-warning btn-sm'>Editar</a>
                                        <form action='excluir.php' method='POST' style='display:inline; margin-left: 5px;'>
                                            <input type='hidden' name='id' value='" . $linha["id_prod"] . "'>
                                            <button type='submit' class='btn btn-danger btn-sm' onclick=\"return confirm('Tem certeza?');\">Excluir</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Nenhum produto encontrado com esse nome.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        endif; 
        $conexao->close();
        ?>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>