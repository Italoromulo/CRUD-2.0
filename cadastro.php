<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de peças de PC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        h2,
        h3 {
            color: #ffffff;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
        }

        .card {
            background-color: #1e1e1e;
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

        .btn {
            border: none;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .d-grid .btn-primary {
            background-color: #0d6efd;
        }

        .d-grid .btn-info {
            background-color: #315CFD;
        }

        .d-grid .btn-secondary {
            background-color: #4a67ff;
        }

        .btn-warning {
            color: #121212;
        }

        .form-control {
            background-color: #2b2b2b;
            color: #e0e0e0;
            border: 1px solid #555;
        }

        .form-control:focus {
            background-color: #333;
            color: #e0e0e0;
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
            border-color: #0a58ca;
        }

        .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: #2b2b2b;
            color: #e0e0e0;
        }
        
        .table-hover>tbody>tr:hover>* {
            background-color: #3c3c3c;
            color: #f0f0f0;
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <h2 class="mt-3">Cadastro de produtos</h2>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">

                <form method="POST" action="salvar.php" enctype="multipart/form-data">

                    <div class="mt-3 form-floating">
                        <input type="text" class="form-control" id="nomeprod" name="nomeprod" placeholder="Nome do produto" required>
                        <label for="nomeprod" class="form-label">Nome do produto</label>
                    </div>

                    <div class="mt-3 form-floating">
                        <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0" placeholder="Valor" required>
                        <label for="preco" class="form-label">Valor (R$)</label>
                    </div>

                    <div class="mt-3 form-floating">
                        <input type="text" class="form-control" id="categorias" name="categorias" placeholder="Categoria" required>
                        <label for="categorias" class="form-label">Categoria (ex: Placa de Vídeo)</label>
                    </div>

                    <div class="mt-3">
                        <label for="arquivo" class="form-label text-white">Imagem do Produto</label>
                        <input type="file" class="form-control" id="arquivo" name="arquivo" accept="image/*" required>
                    </div>

                        <div class="mt-3 form-floating">
                            <input type="text" class="form-control" id="especificacoes" name="especificacoes" placeholder="Especificações" required>
                            <label for="especificacoes" class="form-label">Especificações</label>
                        </div>

                    <div class="mt-3">
                        <div class="row">
                            <div class="col"><button type="reset" class="btn btn-secondary form-control">Limpar</button></div>
                            <div class="col"><button type="submit" class="btn btn-primary form-control">Salvar Produto</button></div>
                        </div>
                    </div>
                </form>

                <a href="inicial.php" class="btn btn-secondary mt-3">Voltar ao Início</a>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>


</html>
