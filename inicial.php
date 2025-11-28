<?php
session_start();
include("conexao.php");

$total_itens_carrinho = 0;
if (isset($_SESSION['carrinho']) && !empty($_SESSION['carrinho'])) {
    $total_itens_carrinho = array_sum($_SESSION['carrinho']);
}

$isAdmin = isset($_SESSION['adm']) && $_SESSION['adm'] == 1;
$estaLogado = isset($_SESSION['id_usuario']);

function renderProduto($row)
{
    global $isAdmin, $estaLogado;

    $imagem = !empty($row['img']) ? "./img/" . htmlspecialchars($row['img']) : "https://via.placeholder.com/280x200?text=Sem+Imagem";

    echo '<article class="carrossel-item">';

    $link = $isAdmin ? 'editar.php?id=' . $row['id_prod'] : 'prod.php?id=' . $row['id_prod'];

    echo '<a href="' . $link . '" class="produto-link">';
    echo '<img src="' . $imagem . '" alt="' . htmlspecialchars($row['nomeprod']) . '">';
    echo '<h3 class="product-name">' . htmlspecialchars($row['nomeprod']) . '</h3>';
    echo '<p class="product-category">' . htmlspecialchars($row['categorias']) . '</p>';
    echo '<div class="product-price"><span>R$ ' . number_format($row['preco'], 2, ',', '.') . '</span></div>';
    echo '</a>';

    echo '<div class="mt-3">';

    if ($isAdmin) {
        echo '<a href="editar.php?id=' . $row['id_prod'] . '" class="btn btn-warning btn-sm w-100 fw-bold">Editar Produto</a>';
    } elseif ($estaLogado) {
        echo '<form action="gerenciar_carrinho.php" method="POST" style="margin:0;">
                <input type="hidden" name="id_prod" value="' . $row['id_prod'] . '">
                <input type="hidden" name="acao" value="adicionar">
                <button type="submit" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                    <i class="fas fa-cart-plus"></i> Adicionar
                </button>
              </form>';
    } else {
        echo '<a href="login.php" class="btn btn-outline-secondary btn-sm w-100 fw-bold">
                <i class="fas fa-lock"></i> Comprar
              </a>';
    }

    echo '</div>';
    echo '</article>';
}
?>
<!DOCTYPE html>
<html lang="pt_br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gygabite Shop - Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="stylesheet" href="style.css">

    <style>
        /* --- ESTILOS ADAPTADOS --- */
        :root {
            --primary-color: #0d6efd;
            --card-bg: #1e1e1e;
            --item-bg: #2b2b2b;
            --text-color: #e0e0e0;
        }

        body {
            background-color: #121212;
            color: var(--text-color);
            scroll-behavior: smooth;
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
            font-size: 1rem;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-link-custom:hover {
            color: #ffffff;
        }

        .nav-link-custom::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: white;
            transition: width 0.3s ease;
        }

        .nav-link-custom:hover::after {
            width: 100%;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cart-icon {
            position: relative;
            color: white;
            font-size: 1.2rem;
            text-decoration: none;
        }

        .cart-icon span {
            position: absolute;
            top: -8px;
            right: -10px;
            background-color: #ffc107;
            color: #000;
            font-size: 0.75rem;
            font-weight: bold;
            border-radius: 50%;
            padding: 2px 6px;
        }

        .card-admin {
            background-color: var(--card-bg);
            border: 1px solid #444;
            color: white;
        }

        .card-header-admin {
            border-bottom: 1px solid #444;
        }

        .product-section {
            padding: 4rem 0 2rem 0;
            width: 100%;
            scroll-margin-top: 80px;
        }

        .product-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }

        .secao-carrossel {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            width: 100%;
        }

        .carrossel-wrapper {
            overflow: hidden;
            padding-inline: 1px;
            width: 100%;
            position: relative;
        }

        .horizontal {
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 20px 5px;
            gap: 20px;
            display: flex;
            flex-wrap: nowrap;
        }

        .horizontal::-webkit-scrollbar {
            display: none;
        }

        .btn-carrossel {
            margin: 10px;
            background: rgba(13, 110, 253, 0.2);
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .btn-carrossel:hover {
            background: var(--primary-color);
            transform: scale(1.1);
        }

        .carrossel-item {
            background-color: var(--item-bg);
            border: 1px solid #444;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            flex-shrink: 0;
            width: 270px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .carrossel-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
            border-color: var(--primary-color);
        }

        .carrossel-item img {
            max-width: 100%;
            height: 180px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .product-name {
            color: white;
            font-size: 1rem;
            min-height: 3em;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 5px;
        }

        .product-category {
            font-size: 0.85rem;
            color: #aaa;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .produto-link {
            text-decoration: none;
            color: inherit;
        }

        /* Footer */
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

        .admin-menu-container {
            position: relative;
            display: inline-block;
        }

        .admin-dropdown-content {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.4s ease, visibility 0.3s;
            position: absolute;
            background-color: #1a1a1aff;
            min-width: 180px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.5);
            z-index: 1001;
            right: 0;
            top: 30px;
            border-radius: 5px;
        }

        .admin-dropdown-content a {
            color: #ff7300;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .admin-dropdown-content a:hover {
            background-color: #181818ff;
            border-radius: 5px;
        }

        .admin-dropdown-content.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

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

        body.light-mode {
            background-color: #f4f4f4;
            color: #000;
        }

        body.light-mode .card-admin,
        body.light-mode .carrossel-item {
            background-color: #fff;
            border-color: #ccc;
            color: #000;
        }

        body.light-mode .product-name,
        body.light-mode h2,
        body.light-mode h3,
        body.light-mode .text-white {
            color: #000 !important;
        }

        body.light-mode .nav-link-custom {
            color: rgba(0, 0, 0, 0.7);
        }

        body.light-mode .nav-link-custom:hover {
            color: #000;
        }

        body.light-mode .admin-dropdown-content {
            background-color: #fff;
            border: 1px solid #ccc;
        }

        body.light-mode .admin-dropdown-content a:hover {
            background-color: #eee;
        }
    </style>
</head>

<body>

    <header class="main-header">
        <div class="container d-flex justify-content-between align-items-center">

            <a href="inicial.php" class="logo">Gygabite <span>shop</span></a>

            <nav class="d-none d-lg-flex gap-4">
                <a href="#" class="nav-link-custom">Início</a>
                <a href="#prod_destaq" class="nav-link-custom">Destaques</a>
                <a href="#perif" class="nav-link-custom">Periféricos</a>
                <a href="#Computadores" class="nav-link-custom">Computadores</a>
                <a href="#geral" class="nav-link-custom">Recentes</a>
            </nav>

            <div class="header-icons">

                <a href="carrinho.php" class="cart-icon" title="Meu Carrinho">
                    <i class="fas fa-shopping-cart"></i>
                    <span><?php echo $total_itens_carrinho; ?></span>
                </a>

                <?php if ($estaLogado): ?>
                    <div class="text-end d-none d-md-block" style="line-height: 1.2;">
                        <span class="text-white d-block" style="font-weight: 500;">
                            Olá, <?php echo htmlspecialchars($_SESSION['login']); ?>
                        </span>
                        <small class="text-white-50" style="font-size: 0.8rem;">
                            <?php echo $isAdmin ? 'Administrador' : 'Cliente'; ?>
                        </small>
                    </div>
                    <a href="logout.php" class="text-white fs-5 ms-2" title="Sair">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>

                    <div class="admin-menu-container">
                        <a href="#" id="admin-menu-button" aria-label="Configurações" title="Configurações">
                            <i class="fas fa-cog"></i>
                        </a>
                        <div id="admin-menu-dropdown" class="admin-dropdown-content">
                            <?php if ($isAdmin) : ?>
                                <a href="sistema.php">Gerenciar Cadastros</a>
                                <a href="log.php">Tela log</a>
                                <a href="painel.php">Dashboard (Relatório)</a>
                            <?php endif; ?>

                            <a href="telabd.php">Modelo BD</a>

                            <?php if (!$isAdmin) : ?>
                                <a onclick="abrirAlterarSenha()" style="cursor:pointer;">Alterar Senha</a>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm fw-bold ms-3">
                        <i class="fas fa-user"></i> Entrar
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </header>

    <div class="container">

        <?php if ($isAdmin): ?>
            <div class="row mb-5 pt-4">
                <div class="col-md-8 offset-md-2">
                    <div class="card card-admin shadow">
                        <div class="card-header card-header-admin text-center">
                            <span class="badge bg-warning text-dark mb-2">Acesso Restrito</span>
                            <h3 class="m-0">Gerenciamento de Produtos</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="cadastro.php" class="btn btn-primary btn-lg px-4">
                                    <i class="fas fa-plus-circle"></i> Cadastrar Novo
                                </a>
                                <a href="consulta.php" class="btn btn-info btn-lg px-4 text-white">
                                    <i class="fas fa-search"></i> Busca Rápida
                                </a>
                                <a href="consul_todos.php" class="btn btn-secondary btn-lg px-4">
                                    <i class="fas fa-list"></i> Ver Tabela Completa
                                </a>
                                <a href="painel.php" class="btn btn-secondary btn-lg px-4">
                                    <i class="fas fa-list"></i> Gráfico
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="border-secondary my-5">
        <?php endif; ?>

        <?php if (!$estaLogado): ?>
            <h2 class="text-center mb-5 text-white pt-3">Bem-vindo à Gygabite Shop</h2>
        <?php else: ?>
            <h2 class="text-center mb-4 text-white">Vitrine de Produtos</h2>
        <?php endif; ?>

        <section id="prod_destaq" class="product-section secao-carrossel">
            <div class="container">
                <h2>Destaques</h2>
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn-carrossel" onclick="scrollCarrossel(-1, 'carrossel-destaques')">❮</button>
                    <div class="carrossel-wrapper">
                        <div class="horizontal" id="carrossel-destaques">
                            <?php
                            $sql = "SELECT * FROM produtos WHERE 
                                    categorias LIKE '%Placas de vídeo%' OR 
                                    categorias LIKE '%Processadores%' OR 
                                    categorias LIKE '%Armazenamento%' OR 
                                    categorias LIKE '%Memória RAM%' OR 
                                    categorias LIKE '%Monitor%' 
                                    ORDER BY id_prod DESC LIMIT 10";

                            $result = $conexao->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) renderProduto($row);
                            } else {
                                echo "<p class='text-white text-center w-100'>Nenhum destaque encontrado.</p>";
                            }
                            ?>
                        </div>
                    </div>
                    <button class="btn-carrossel" onclick="scrollCarrossel(1, 'carrossel-destaques')">❯</button>
                </div>
            </div>
        </section>

        <section id="perif" class="product-section secao-carrossel">
            <div class="container">
                <h2>Periféricos</h2>
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn-carrossel" onclick="scrollCarrossel(-1, 'carrossel-perifericos')">❮</button>
                    <div class="carrossel-wrapper">
                        <div class="horizontal" id="carrossel-perifericos">
                            <?php
                            $sql = "SELECT * FROM produtos WHERE categorias LIKE '%Teclado%' OR categorias LIKE '%Mouse%' OR categorias LIKE '%Headset%' OR categorias LIKE '%Microfones%' OR categorias LIKE '%Pen Drive%' LIMIT 10";
                            $result = $conexao->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) renderProduto($row);
                            } else {
                                echo "<p class='text-white text-center w-100'>Nenhum periférico encontrado.</p>";
                            }
                            ?>
                        </div>
                    </div>
                    <button class="btn-carrossel" onclick="scrollCarrossel(1, 'carrossel-perifericos')">❯</button>
                </div>
            </div>
        </section>

        <section id="Computadores" class="product-section secao-carrossel">
            <div class="container">
                <h2>Computadores</h2>
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn-carrossel" onclick="scrollCarrossel(-1, 'carrossel-Computadores')">❮</button>
                    <div class="carrossel-wrapper">
                        <div class="horizontal" id="carrossel-Computadores">
                            <?php
                            $sql = "SELECT * FROM produtos WHERE categorias LIKE '%Computadores%' LIMIT 10";
                            $result = $conexao->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) renderProduto($row);
                            } else {
                                echo "<p class='text-white text-center w-100'>Nenhum computador encontrado.</p>";
                            }
                            ?>
                        </div>
                    </div>
                    <button class="btn-carrossel" onclick="scrollCarrossel(1, 'carrossel-Computadores')">❯</button>
                </div>
            </div>
        </section>

        <section id="geral" class="product-section secao-carrossel">
            <div class="container">
                <h2>Produtos Recentes</h2>
                <div class="d-flex align-items-center justify-content-center">
                    <button class="btn-carrossel" onclick="scrollCarrossel(-1, 'carrossel-geral')">❮</button>
                    <div class="carrossel-wrapper">
                        <div class="horizontal" id="carrossel-geral">
                            <?php
                            $sql = "SELECT * FROM produtos ORDER BY id_prod DESC LIMIT 15";
                            $result = $conexao->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) renderProduto($row);
                            } else {
                                echo "<p class='text-white text-center w-100'>Nenhum produto cadastrado ainda.</p>";
                            }
                            ?>
                        </div>
                    </div>
                    <button class="btn-carrossel" onclick="scrollCarrossel(1, 'carrossel-geral')">❯</button>
                </div>
            </div>
        </section>

    </div>

    <footer class="main-footer">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h4>Gygabite Shop</h4>
                    <p>A sua paixão por hardware começa aqui.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h4>Links Rápidos</h4>
                    <ul class="list-unstyled">
                        <?php if ($isAdmin): ?>
                            <li><a href="cadastro.php" class="footer-link">Painel Admin</a></li>
                            <li><a href="consul_todos.php" class="footer-link">Gerenciar Produtos</a></li>
                        <?php elseif ($estaLogado): ?>
                            <li><a href="#" class="footer-link">Minha Conta</a></li>
                            <li><a href="carrinho.php" class="footer-link">Meu Carrinho</a></li>
                        <?php else: ?>
                            <li><a href="login.php" class="footer-link">Fazer Login</a></li>
                            <li><a href="cadastrouser.php" class="footer-link">Criar Conta</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>Sistema</h4>
                    <p>&copy; 2025 Gygabite Shop Dev Team</p>
                </div>
                <div class="footer-section">
                    <h4>Siga-nos</h4>
                    <div class="social-icons">
                        <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://x.com/"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.instagram.com/romulo1st/"><i class="fab fa-instagram"></i></a>
                    </div>
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
        function scrollCarrossel(direction, carrosselId) {
            const carrossel = document.getElementById(carrosselId);
            const itemWidth = 280;
            carrossel.scrollBy({
                left: itemWidth * direction * 2,
                behavior: 'smooth'
            });
        }

        // === CÓDIGO DO MENU DE ENGRENAGEM (ORIGINAL) ===
        const adminButton = document.getElementById('admin-menu-button');
        if (adminButton) {
            adminButton.addEventListener('click', function(event) {
                event.preventDefault();
                const dropdown = document.getElementById('admin-menu-dropdown');
                dropdown.classList.toggle('show');
            });
        }

        window.addEventListener('click', function(event) {
            if (!event.target.closest('#admin-menu-button')) {
                const dropdowns = document.querySelectorAll('.admin-dropdown-content');
                dropdowns.forEach(dropdown => {
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                });
            }
        });

        function abrirAlterarSenha() {
            const width = 400;
            const height = 500;
            const left = (screen.width / 2) - (width / 2);
            const top = (screen.height / 2) - (height / 2);
            window.open('alterarSenha.php', 'alterarSenha', `width=${width},height=${height},top=${top},left=${left},resizable=yes,scrollbars=yes`);
        }

        const body = document.body;
        const btnTheme = document.getElementById('toggle-theme');
        const btnInc = document.getElementById('increase-font');
        const btnDec = document.getElementById('decrease-font');
        
        if (localStorage.getItem('theme') === 'light') {
            body.classList.add('light-mode');
        }

        btnTheme.addEventListener('click', () => {
            body.classList.toggle('light-mode');
            localStorage.setItem('theme', body.classList.contains('light-mode') ? 'light' : 'dark');
        });

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
