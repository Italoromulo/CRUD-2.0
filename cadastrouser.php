<?php
session_start();
include_once('conexao.php');

function validaCPF($cpf)
{
    $cpf = preg_replace('/[^0-9]/is', '', $cpf);
    if (strlen($cpf) != 11) return false;
    if (preg_match('/(\d)\1{10}/', $cpf)) return false;
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) return false;
    }
    return true;
}

$mensagem_erro = '';
if (isset($_SESSION['msg'])) {
    $mensagem_erro = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

if (isset($_POST['submit'])) {
    $nome          = $_POST['nome'] ?? '';
    $email         = $_POST['email'] ?? '';
    $telefone      = $_POST['telefone'] ?? '';
    $cpf           = $_POST['cpf'] ?? '';
    $login         = $_POST['login'] ?? '';
    $senha_plana   = $_POST['senha'] ?? '';
    $confirmaSenha = $_POST['confirmaSenha'] ?? '';

    $erros = [];

    if (empty($nome) || empty($login) || empty($senha_plana) || empty($email) || empty($telefone) || empty($cpf)) {
        $erros[] = "Preencha todos os campos obrigatórios.";
    }
    if (strlen($nome) < 10 || strlen($nome) > 100) $erros[] = "Nome: Deve ter entre 10 e 100 caracteres.";

    if (!validaCPF($cpf)) $erros[] = "CPF inválido.";

    $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
    if (strlen($telefone_limpo) < 10) $erros[] = "Telefone inválido.";

    if (strlen($login) < 4 || strlen($login) > 45) $erros[] = "Login: Deve ter entre 4 e 45 caracteres.";

    if (strlen($senha_plana) < 6) $erros[] = "Senha: Deve ter no mínimo 6 caracteres.";

    if ($senha_plana !== $confirmaSenha) $erros[] = "As senhas não coincidem.";

    if (!empty($erros)) {
        $_SESSION['msg'] = implode("<br>", $erros);
        header("Location: cadastrouser.php");
        exit;
    } else {
        $senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);

        if ($conexao->connect_error) {
            die("Falha na conexão: " . $conexao->connect_error);
        }

        $sql = "INSERT INTO usuarios (nome, email, senha, login, cpf, telefone) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        if ($stmt === false) {
            $_SESSION['msg'] = "Erro interno no banco: " . $conexao->error;
            header("Location: cadastrouser.php");
            exit;
        }

        $stmt->bind_param("ssssss", $nome, $email, $senha_hash, $login, $cpf, $telefone_limpo);

        if ($stmt->execute()) {
            $_SESSION['msg'] = "Cadastro realizado com sucesso! Faça login.";
            header("Location: login.php");
            exit;
        } else {
            if ($conexao->errno == 1062) {
                $_SESSION['msg'] = "Erro: Email ou Login já cadastrados.";
            } else {
                $_SESSION['msg'] = "Erro ao cadastrar: " . $stmt->error;
            }
            header("Location: cadastrouser.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Gygabite Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
            transition: background-color 0.3s, color 0.3s;
        }

        .content-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
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
            font-size: 1.2rem;
            color: #666666ff;
        }

        .form-control {
            background-color: #313131ff;
            color: #e0e0e0 !important;
            border: 1px solid #555;
        }

        ::placeholder {
            color: #888888ff !important;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background-color: #313131ff;
            color: #e0e0e0 !important;
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

        .social-icons a {
            color: var(--text-color);
            font-size: 1.5rem;
            margin: 0 10px;
        }

        .social-icons a:hover {
            color: #0246adff;
            transition: color 0.3s ease;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 1rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* --- CSS DE ACESSIBILIDADE ADICIONADO --- */
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

        /* MODO CLARO (LIGHT MODE) */
        body.light-mode {
            background-color: #f4f4f4;
            color: #000;
        }

        body.light-mode .card-custom {
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        body.light-mode .card-header-custom h2 {
            color: var(--primary-color);
        }

        body.light-mode .form-label {
            color: #333;
        }

        body.light-mode .form-control {
            background-color: #fff;
            color: #000 !important;
            border: 1px solid #ccc;
        }

        body.light-mode .form-control:focus {
            background-color: #fff;
        }
    </style>
</head>

<body>

    <header class="main-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="inicial.php" class="logo">Gygabite <span>shop</span></a>
            <a href="login.php" class="text-white fs-5 text-decoration-none" title="Login">
                <i class="fas fa-user"></i> Entrar
            </a>
        </div>
    </header>

    <div class="container content-wrapper">
        <div class="row justify-content-center w-100">
            <div class="col-lg-8 col-md-10">
                <div class="card card-custom p-4">

                    <div class="card-header-custom">
                        <h2>Criar Conta</h2>
                    </div>

                    <?php if (!empty($mensagem_erro)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Atenção:</strong><br><?php echo $mensagem_erro; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form id="cadastroForm" action="cadastrouser.php" method="POST">
                        <div class="row g-3">

                            <div class="col-12">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite seu nome completo" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                            </div>

                            <div class="col-md-6">
                                <label for="telefone" class="form-label">Telefone/Celular</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="(99) 99999-9999" required>
                            </div>

                            <div class="col-12">
                                <label for="cpf" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                            </div>

                            <div class="col-12">
                                <hr class="text-muted">
                            </div>

                            <div class="col-md-4">
                                <label for="login" class="form-label">Login</label>
                                <input type="text" class="form-control" id="login" name="login" placeholder="Crie seu login" required>
                            </div>

                            <div class="col-md-4">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                            </div>

                            <div class="col-md-4">
                                <label for="confirmaSenha" class="form-label">Confirmar Senha</label>
                                <input type="password" class="form-control" id="confirmaSenha" name="confirmaSenha" required>
                            </div>

                            <div class="col-12 mt-4 d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-secondary me-md-2">Limpar</button>
                                <button type="submit" name="submit" class="btn btn-primary px-5 fw-bold">CADASTRAR</button>
                            </div>
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
                    <p>A sua paixão por hardware começa aqui.</p>
                </div>
                <h4>Siga-nos</h4>
                <div class="social-icons">
                    <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://x.com/"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com/romulo1st/"><i class="fab fa-instagram"></i></a>
                </div>
                <div class="footer-section col-md-4">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="inicial.php">Ir para a Loja</a></li>
                        <li><a href="#">Política de Privacidade</a></li>
                    </ul>
                </div>
                <div class="footer-section col-md-4">

                </div>
            </div>
            <div class="footer-bottom">
                &copy; 2025 Gygabite Shop. Todos os direitos reservados.
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
        function aplicarMascaraCPF(valor) {
            return valor.replace(/\D/g, '')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }

        function aplicarMascaraTelefone(valor) {
            let r = valor.replace(/\D/g, '');
            if (r.length > 11) r = r.slice(0, 11);

            if (r.length > 10) {
                r = r.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            } else if (r.length > 5) {
                r = r.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            } else if (r.length > 2) {
                r = r.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
            } else {
                r = r.replace(/^(\d*)/, '$1');
            }
            return r;
        }

        document.getElementById('cpf').addEventListener('input', e => e.target.value = aplicarMascaraCPF(e.target.value));
        document.getElementById('telefone').addEventListener('input', e => e.target.value = aplicarMascaraTelefone(e.target.value));

        // --- SCRIPT DE ACESSIBILIDADE (ADICIONADO) ---
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
