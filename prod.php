<?php
session_start();
include("conexao.php");

$total_itens_carrinho = 0;
if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
    $total_itens_carrinho = array_sum($_SESSION['carrinho']);
}

if (!isset($_GET['id'])) {
    header("Location: inicial.php");
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM produtos WHERE id_prod = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Produto não encontrado.'); window.location.href='inicial.php';</script>";
    exit;
}

$produto = $result->fetch_assoc();

$isAdmin = isset($_SESSION['adm']) && $_SESSION['adm'] == 1;
$estaLogado = isset($_SESSION['id_usuario']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produto['nomeprod']); ?> - Detalhes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        /* ESTILOS GERAIS DO TEMA */
        :root {
            --primary-color: #0d6efd;
            --card-bg: #1e1e1e;
            --text-color: #e0e0e0;
        }

        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            transition: background-color 0.3s, color 0.3s;
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

        /* LAYOUT PRODUTO */
        .produto-imagem img {
            max-width: 100%;
            border-radius: 10px;
            border: 1px solid #444;
            background: #fff;
            padding: 10px;
        }

        .price-box {
            background-color: #1e1e1e;
            border: 1px solid #444;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .price-box p {
            color: #ccc !important;
        }

        .preco-destaque {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
        }

        .especificacoes-box {
            background-color: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            margin-top: 40px;
            border: 1px solid #444;
        }

        /* FOOTER */
        .main-footer {
            background-color: var(--primary-color);
            color: white;
            padding: 2rem 0;
            margin-top: 4rem;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .footer-link:hover {
            color: white;
        }

        .social-icons a {
            color: var(--text-color);
            font-size: 1.5rem;
            margin: 0 10px;
        }

        .social-icons a:hover {
            color: #0246adff;
            transition: color 0.3s ease;
        }

        /* ==================================================================
           NOVO CSS DE ACESSIBILIDADE (ADICIONADO)
           ================================================================== */
        .accessibility-menu {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.85);
            padding: 10px;
            border-radius: 8px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            border: 1px solid #444;
        }

        .accessibility-btn {
            background: transparent;
            border: 1px solid #fff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: bold;
            transition: all 0.2s;
        }

        .accessibility-btn:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* MODO CLARO (LIGHT MODE) - SOBRESCRITAS */
        body.light-mode {
            background-color: #f4f4f4;
            color: #000;
        }

        body.light-mode .price-box,
        body.light-mode .especificacoes-box {
            background-color: #ffffff;
            border-color: #ddd;
            color: #333;
        }

        body.light-mode .price-box p,
        body.light-mode .especificacoes-box p,
        body.light-mode .especificacoes-box h3,
        body.light-mode .text-white {
            color: #333 !important;
        }

        body.light-mode .nav-link-custom {
            color: rgba(0, 0, 0, 0.7);
        }

        body.light-mode .nav-link-custom:hover {
            color: #000;
        }

        body.light-mode .text-white-50 {
            color: #666 !important;
        }

        body.light-mode i.fas.fa-shopping-cart,
        body.light-mode i.fas.fa-sign-out-alt {
            color: #333;
            /* Ícones escuros no modo claro se header mudar */
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

                <?php if ($estaLogado): ?>
                    <div class="text-end d-none d-md-block" style="line-height: 1.2;">
                        <span class="text-white d-block fw-bold">Olá, <?php echo htmlspecialchars($_SESSION['login']); ?></span>
                        <small class="text-white-50" style="font-size: 0.75rem;">
                            <?php echo $isAdmin ? 'Administrador' : 'Cliente'; ?>
                        </small>
                    </div>
                    <a href="logout.php" class="text-white fs-5" title="Sair"><i class="fas fa-sign-out-alt"></i></a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm">Entrar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 mb-4 text-center produto-imagem">
                <?php $img = !empty($produto['img']) ? "./img/" . $produto['img'] : "https://via.placeholder.com/500"; ?>
                <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($produto['nomeprod']); ?>">
            </div>

            <div class="col-md-6">
                <h1 class="mb-2"><?php echo htmlspecialchars($produto['nomeprod']); ?></h1>
                <span class="badge bg-secondary mb-3"><?php echo htmlspecialchars($produto['categorias']); ?></span>

                <div class="price-box">
                    <p class="text-muted mb-0">À vista no PIX</p>
                    <div class="preco-destaque">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></div>
                    <p class="mb-4">ou parcelado no cartão</p>

                    <?php if ($estaLogado): ?>
                        <form action="gerenciar_carrinho.php" method="POST">
                            <input type="hidden" name="id_prod" value="<?php echo $produto['id_prod']; ?>">
                            <input type="hidden" name="acao" value="adicionar">
                            <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">
                                <i class="fas fa-cart-plus"></i> Adicionar ao Carrinho
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary btn-lg w-100">Faça Login para Comprar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($produto['especificacoes'])): ?>
            <div class="row">
                <div class="col-12">
                    <div class="especificacoes-box">
                        <h3 class="border-bottom pb-2 mb-3 text-white">Especificações Técnicas</h3>
                        <p style="white-space: pre-line; color: #ccc;">
                            <?php echo htmlspecialchars($produto['especificacoes']); ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-4 mb-5">
            <a href="inicial.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Voltar para a Loja</a>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h4>Gygabite Shop</h4>
                    <p>Sua loja de hardware.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h4>Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="inicial.php" class="footer-link">Início</a></li>
                        <li><a href="carrinho.php" class="footer-link">Carrinho</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <p>&copy; 2025 Gygabite Shop</p>
                </div>
                <h4>Siga-nos</h4>
                <div class="social-icons">
                    <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://x.com/"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com/romulo1st/"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <div class="accessibility-menu">
        <button id="toggle-theme" class="accessibility-btn">🌓 Tema</button>
        <button id="increase-font" class="accessibility-btn">A+</button>
        <button id="decrease-font" class="accessibility-btn">A-</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const body = document.body;
        const btnTheme = document.getElementById('toggle-theme');
        const btnInc = document.getElementById('increase-font');
        const btnDec = document.getElementById('decrease-font');

        // 1. Carregar tema salvo
        if (localStorage.getItem('theme') === 'light') {
            body.classList.add('light-mode');
        }

        // 2. Alternar Tema
        btnTheme.addEventListener('click', () => {
            body.classList.toggle('light-mode');
            localStorage.setItem('theme', body.classList.contains('light-mode') ? 'light' : 'dark');
        });

        // 3. Tamanho da Fonte
        let currentFont = 100;
        btnInc.addEventListener('click', () => {
            if (currentFont < 150) {
                currentFont += 10;
                document.documentElement.style.fontSize = currentFont + '%';
            }
        });
        btnDec.addEventListener('click', () => {
            if (currentFont > 70) {
                currentFont -= 10;
                document.documentElement.style.fontSize = currentFont + '%';
            }
        });
    </script>
</body>

</html>
