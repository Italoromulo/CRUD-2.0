<?php
session_start();
include("conexao.php");

// Verifica se está logado
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

// Contagem de itens no header
$total_itens_carrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $total_itens_carrinho = array_sum($_SESSION['carrinho']);
}

$total_geral = 0;
$isAdmin = isset($_SESSION['adm']) && $_SESSION['adm'] == 1;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho - Gygabite Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>

        :root {
            --primary-color: #0d6efd;
            --card-bg: #1e1e1e;
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-header {
            background-color: var(--primary-color);
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 900;
            color: white;
            text-decoration: none;
        }

        .logo span {
            color: #cff4fc;
        }

        .nav-link-custom {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-weight: 600;
            margin: 0 10px;
            transition: color 0.3s;
        }

        .nav-link-custom:hover {
            color: #fff;
            text-decoration: underline;
        }

        .carrinho-container {
            background-color: #1e1e1e;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #444;
        }

        .item-carrinho {
            border-bottom: 1px solid #333;
            padding: 15px 0;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        .item-carrinho small {
            color: #ccc !important;
        }

        .img-mini {
            width: 80px;
            height: 80px;
            object-fit: contain;
            background: #fff;
            border-radius: 5px;
            padding: 5px;
        }

        .main-footer {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem 0;
            margin-top: auto;
            width: 100%;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .footer-link:hover {
            color: white;
        }
    </style>
</head>

<body>

    <header class="main-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="inicial.php" class="logo">Gygabite <span>shop</span></a>

            <nav class="d-none d-lg-flex">
                <a href="inicial.php" class="nav-link-custom">Início</a>
                <a href="inicial.php#prod_destaq" class="nav-link-custom">Destaques</a>
                <a href="inicial.php#perif" class="nav-link-custom">Periféricos</a>
                <a href="inicial.php#Computadores" class="nav-link-custom">Computadores</a>
            </nav>

            <div class="d-flex align-items-center gap-3">
                <a href="carrinho.php" class="text-white fs-5 position-relative text-decoration-none">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                        <?php echo $total_itens_carrinho; ?>
                    </span>
                </a>
                <div class="text-end d-none d-md-block" style="line-height: 1.2;">
                    <span class="text-white d-block fw-bold">Olá, <?php echo htmlspecialchars($_SESSION['login']); ?></span>
                    <small class="text-white-50" style="font-size: 0.75rem;">
                        <?php echo $isAdmin ? 'Administrador' : 'Cliente'; ?>
                    </small>
                </div>
                <a href="logout.php" class="text-white fs-5" title="Sair"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </header>

    <div class="container flex-grow-1">
        <h2 class="mb-4 text-white"><i class="fas fa-shopping-cart"></i> Carrinho de Compras</h2>

        <?php if (empty($_SESSION['carrinho'])): ?>
            <div class="alert alert-info text-center p-5 bg-dark text-white border-secondary">
                <h4>Seu carrinho está vazio!</h4>
                <a href="inicial.php" class="btn btn-primary mt-3">Ir às Compras</a>
            </div>
        <?php else: ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="carrinho-container">
                        <?php
                        $ids = implode(',', array_keys($_SESSION['carrinho']));
                        if (!empty($ids)) {
                            $sql = "SELECT * FROM produtos WHERE id_prod IN ($ids)";
                            $result = $conexao->query($sql);

                            while ($row = $result->fetch_assoc()):
                                $id = $row['id_prod'];
                                $qtd = $_SESSION['carrinho'][$id];
                                $subtotal = $row['preco'] * $qtd;
                                $total_geral += $subtotal;
                                $img = !empty($row['img']) ? "./img/" . $row['img'] : "https://via.placeholder.com/80";
                        ?>

                                <div class="item-carrinho">
                                    <div class="me-3">
                                        <img src="<?php echo $img; ?>" class="img-mini" alt="Foto">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="text-white mb-1"><?php echo htmlspecialchars($row['nomeprod']); ?></h5>
                                        <small class="text-muted">Unitário: R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></small>
                                    </div>

                                    <div class="d-flex align-items-center gap-2 mx-3">
                                        <form action="gerenciar_carrinho.php" method="POST" class="d-inline">
                                            <input type="hidden" name="acao" value="remover_um">
                                            <input type="hidden" name="id_prod" value="<?php echo $id; ?>">
                                            <button class="btn btn-outline-secondary btn-sm">-</button>
                                        </form>

                                        <span class="fw-bold mx-2 text-white"><?php echo $qtd; ?></span>

                                        <form action="gerenciar_carrinho.php" method="POST" class="d-inline">
                                            <input type="hidden" name="acao" value="adicionar">
                                            <input type="hidden" name="id_prod" value="<?php echo $id; ?>">
                                            <button class="btn btn-outline-secondary btn-sm">+</button>
                                        </form>
                                    </div>

                                    <div class="text-end ms-3" style="min-width: 100px;">
                                        <h5 class="text-primary mb-0">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></h5>
                                        <form action="gerenciar_carrinho.php" method="POST" class="mt-1">
                                            <input type="hidden" name="acao" value="remover_produto">
                                            <input type="hidden" name="id_prod" value="<?php echo $id; ?>">
                                            <button class="btn btn-link text-danger p-0 text-decoration-none btn-sm">Remover</button>
                                        </form>
                                    </div>
                                </div>
                        <?php
                            endwhile;
                        }
                        ?>
                    </div>

                    <div class="mt-3 text-end">
                        <form action="gerenciar_carrinho.php" method="POST">
                            <input type="hidden" name="acao" value="limpar">
                            <button class="btn btn-outline-danger btn-sm">Esvaziar Carrinho</button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card bg-dark text-white border-secondary">
                        <div class="card-header bg-primary text-white fw-bold">Resumo</div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Frete</span>
                                <span class="text-success">Grátis</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <strong class="fs-4">Total</strong>
                                <strong class="fs-4 text-primary">R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></strong>
                            </div>

                            <a href="checkout.php" class="btn btn-success w-100 btn-lg fw-bold">Finalizar Compra</a>
                            <a href="inicial.php" class="btn btn-outline-light w-100 mt-2">Continuar Comprando</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <footer class="main-footer">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h4>Gygabite Shop</h4>
                    <p>Painel do Cliente</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h4>Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="inicial.php" class="footer-link">Loja</a></li>
                        <li><a href="carrinho.php" class="footer-link">Carrinho</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <p>&copy; 2025 Gygabite Shop</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>