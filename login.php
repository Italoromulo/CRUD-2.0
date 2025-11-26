<?php
session_start();
include_once('conexao.php');

$erro_login = '';

// Se já estiver logado, redireciona para a página INICIAL
if (isset($_SESSION['login']) && isset($_SESSION['id_usuario'])) {
    header("Location: inicial.php");
    exit;
}

if (isset($_POST['submit'])) {
    $usuario_login = $_POST['usuario'];
    $senha_digitada = $_POST['senha'];

    if (empty($usuario_login) || empty($senha_digitada)) {
        $erro_login = "Preencha todos os campos.";
    } else {
        // 1. MUDANÇA AQUI: Adicionei ', adm' no SELECT
        $sql = "SELECT id_usuario, nome, login, senha, adm FROM usuarios WHERE login = ? OR email = ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $usuario_login, $usuario_login);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $dados_usuario = $resultado->fetch_assoc();

            if (password_verify($senha_digitada, $dados_usuario['senha'])) {
                // Login Sucesso
                $_SESSION['id_usuario'] = $dados_usuario['id_usuario'];
                $_SESSION['nome']       = $dados_usuario['nome'];
                $_SESSION['login']      = $dados_usuario['login'];

                // 2. MUDANÇA AQUI: Salvando o nível de acesso na sessão
                $_SESSION['adm']        = $dados_usuario['adm'];

                header("Location: inicial.php");
                exit;
            } else {
                $erro_login = "Senha incorreta.";
            }
        } else {
            $erro_login = "Usuário ou E-mail não encontrado.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gygabite Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap');

        :root {
            --primary-color: #0d6efd;
            --header-bg: #0d6efd;
            --footer-bg: #0d6efd;
            --accent-hover: #cff4fc;
        }

        body {
            background-color: #000000;
            font-family: 'Roboto', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-header {
            background-color: var(--header-bg);
            padding: 1rem 0;
            border-bottom: 1px solid #0a58ca;
            margin-bottom: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 900;
            color: white;
            text-decoration: none;
        }

        .logo span {
            color: var(--accent-hover);
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

        .card-custom {
            background-color: rgba(49, 49, 49, 0.5);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.2);
            border: none;
            margin: 40px;
        }

        .card-header-custom {
            background-color: transparent;
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .card-header-custom h2 {
            color: #ffffffff;
            font-weight: 800;
            text-transform: uppercase;
        }

        .form-label {
            font-weight: 600;
            font-size: 1.1rem;
            color: #ccc;
        }

        .form-control {
            background-color: #313131ff;
            color: #e0e0e0 !important;
            border: 1px solid #444;
            padding: 12px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background-color: #313131ff;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        ::placeholder {
            color: #888888ff !important;
        }

        .btn-primary {
            padding: 12px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .main-footer {
            background-color: var(--footer-bg);
            padding: 3rem 0 1rem;
            border-top: 1px solid #0a58ca;
            color: white;
            margin-top: 3rem;
        }

        .footer-content h4 {
            color: var(--accent-hover);
            font-weight: 700;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li a,
        .social-icons a {
            color: white;
            text-decoration: none;
        }

        .footer-section ul li a:hover,
        .social-icons a:hover {
            color: var(--accent-hover);
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 1rem;
            color: rgba(255, 255, 255, 0.7);
        }

    </style>
</head>

<body>
    <header class="main-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="inicial.php" class="logo">Gygabite <span>shop</span></a>
            <nav class="d-none d-lg-flex gap-4">
                <a href="inicial.php" class="nav-link-custom">Início</a>
            </nav>
            <a href="cadastrouser.php" class="text-white fs-5 text-decoration-none" title="Criar Conta">
                <i class="fas fa-user-plus"></i> Criar Conta
            </a>
        </div>
    </header>
    <div class="container content-wrapper">
        <div class="row justify-content-center w-100">
            <div class="col-lg-5 col-md-8">
                <?php if (isset($_SESSION['msg'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['msg'];
                        unset($_SESSION['msg']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <div class="card card-custom p-4">
                    <div class="card-header-custom">
                        <h2>Acessar Conta</h2>
                    </div>
                    <?php if (!empty($erro_login)): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div><?php echo $erro_login; ?></div>
                        </div>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Login ou E-mail</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white border-secondary"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Digite seu login" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="senha" class="form-label">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white border-secondary"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" required>
                            </div>
                        </div>
                        <div class="text-end mb-3">
                            <a href="esqueci_senha.php" class="text-decoration-none text-white-50 hover-link small">Esqueceu a senha?</a>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="submit" class="btn btn-primary fw-bold shadow">ENTRAR</button>
                        </div>
                        <div class="text-center mt-3">
                            <a href="cadastrouser.php" class="text-decoration-none text-white-50 hover-link">Não tem uma conta? <span class="text-white fw-bold">Cadastre-se</span></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content row text-center text-md-start">
                <div class="footer-section col-md-4">
                    <h4>Gygabite Shop</h4>
                    <p>Acesse sua conta para ver ofertas exclusivas.</p>
                </div>
                <div class="footer-section col-md-4">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="inicial.php">Ir para a Loja</a></li>
                        <li><a href="cadastrouser.php">Criar Conta</a></li>
                    </ul>
                </div>
                <div class="footer-section col-md-4">
                    <h4>Siga-nos</h4>
                    <div class="social-icons"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-twitter"></i></a><a href="#"><i class="fab fa-instagram"></i></a></div>
                </div>
            </div>
            <div class="footer-bottom">&copy; 2025 Gygabite Shop. Todos os direitos reservados.</div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>