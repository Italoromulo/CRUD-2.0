<?php
session_start();
include_once('conexao.php');

$mensagem = '';
$tipo_msg = ''; // 'danger' ou 'success'

// Função para limpar caracteres do CPF (deixar só números para comparar ou salvar)
// Nota: Se no seu banco o CPF tem pontos e traços, não use essa função no SQL. 
// Baseado no seu cadastro, vou assumir que você salva com a máscara ou sem. 
// Vou aplicar a máscara no front-end para garantir.

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $nova_senha = $_POST['nova_senha'];
    $confirma_senha = $_POST['confirma_senha'];

    // 1. Validação básica
    if (empty($email) || empty($cpf) || empty($nova_senha) || empty($confirma_senha)) {
        $mensagem = "Preencha todos os campos.";
        $tipo_msg = "danger";
    } 
    elseif ($nova_senha !== $confirma_senha) {
        $mensagem = "As senhas não conferem.";
        $tipo_msg = "danger";
    } 
    elseif (strlen($nova_senha) < 6) {
        $mensagem = "A senha deve ter no mínimo 6 caracteres.";
        $tipo_msg = "danger";
    } 
    else {
        // 2. Verifica se o usuário existe com esse Email E CPF
        $sql = "SELECT id_usuario FROM usuarios WHERE email = ? AND cpf = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $email, $cpf);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Usuário encontrado! Vamos atualizar a senha.
            $row = $resultado->fetch_assoc();
            $id_usuario = $row['id_usuario'];

            // Criptografa a nova senha
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            $sql_update = "UPDATE usuarios SET senha = ? WHERE id_usuario = ?";
            $stmt_update = $conexao->prepare($sql_update);
            $stmt_update->bind_param("si", $senha_hash, $id_usuario);

            if ($stmt_update->execute()) {
                $mensagem = "Senha alterada com sucesso! <a href='login.php' class='alert-link'>Faça login aqui</a>.";
                $tipo_msg = "success";
            } else {
                $mensagem = "Erro ao atualizar senha no banco de dados.";
                $tipo_msg = "danger";
            }
        } else {
            $mensagem = "Dados incorretos. Verifique se o E-mail e o CPF correspondem a uma conta válida.";
            $tipo_msg = "danger";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Gygabite Shop</title>
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

        .logo span { color: var(--accent-hover); }

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
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
        }

        .form-label {
            font-weight: 600;
            font-size: 1.1rem;
            color: #ccc;
        }

        .form-control {
            background-color: #313131;
            color: #e0e0e0 !important;
            border: 1px solid #444;
            padding: 12px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background-color: #313131;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        ::placeholder { color: #888888 !important; }

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
        
        .footer-content h4 { color: var(--accent-hover); font-weight: 700; }
        .footer-section ul { list-style: none; padding: 0; }
        .footer-section ul li a, .social-icons a { color: white; text-decoration: none; }
        .footer-bottom { text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.2); padding-top: 1rem; color: rgba(255, 255, 255, 0.7); }
    </style>
</head>

<body>

    <header class="main-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="inicial.php" class="logo">Gygabite <span>shop</span></a>
            <a href="login.php" class="text-white fs-5 text-decoration-none" title="Login">
                <i class="fas fa-sign-in-alt"></i> Voltar ao Login
            </a>
        </div>
    </header>

    <div class="container content-wrapper">
        <div class="row justify-content-center w-100">
            <div class="col-lg-5 col-md-8">
                
                <?php if (!empty($mensagem)): ?>
                    <div class="alert alert-<?php echo $tipo_msg; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensagem; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card card-custom p-4">
                    <div class="card-header-custom">
                        <h2>Recuperar Senha</h2>
                    </div>
                    
                    <p class="text-white-50 text-center mb-4">Confirme seus dados para redefinir sua senha.</p>

                    <form action="esqueci_senha.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail Cadastrado</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white border-secondary"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white border-secondary"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                            </div>
                        </div>

                        <hr class="border-secondary my-4">

                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white border-secondary"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="nova_senha" name="nova_senha" placeholder="Mínimo 6 caracteres" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="confirma_senha" class="form-label">Confirmar Nova Senha</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark text-white border-secondary"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirma_senha" name="confirma_senha" placeholder="Repita a senha" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="submit" class="btn btn-primary fw-bold shadow">REDEFINIR SENHA</button>
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
                    <p>Recuperação de conta segura.</p>
                </div>
                <div class="footer-section col-md-4">
                    <h4>Links Rápidos</h4>
                    <ul>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="cadastrouser.php">Criar Conta</a></li>
                    </ul>
                </div>
                <div class="footer-section col-md-4">
                    <h4>Siga-nos</h4>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; 2025 Gygabite Shop. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mesma máscara de CPF do cadastro para garantir compatibilidade
        function aplicarMascaraCPF(valor) {
            return valor.replace(/\D/g, '')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d)/, '$1.$2')
                .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }
        document.getElementById('cpf').addEventListener('input', e => e.target.value = aplicarMascaraCPF(e.target.value));
    </script>
</body>
</html>