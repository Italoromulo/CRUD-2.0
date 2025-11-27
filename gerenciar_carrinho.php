<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['acao'])) {
        
        $acao = $_POST['acao'];
        
        if ($acao == 'adicionar' && isset($_POST['id_prod'])) {
            $id = intval($_POST['id_prod']);
            if (!isset($_SESSION['carrinho'][$id])) {
                $_SESSION['carrinho'][$id] = 1;
            } else {
                $_SESSION['carrinho'][$id] += 1;
            }
        }

        if ($acao == 'remover_um' && isset($_POST['id_prod'])) {
            $id = intval($_POST['id_prod']);
            if (isset($_SESSION['carrinho'][$id])) {
                $_SESSION['carrinho'][$id] -= 1;
                if ($_SESSION['carrinho'][$id] <= 0) {
                    unset($_SESSION['carrinho'][$id]);
                }
            }
        }

        if ($acao == 'remover_produto' && isset($_POST['id_prod'])) {
            $id = intval($_POST['id_prod']);
            if (isset($_SESSION['carrinho'][$id])) {
                unset($_SESSION['carrinho'][$id]);
            }
        }

        if ($acao == 'limpar') {
            unset($_SESSION['carrinho']);
        }
    }
}

if(isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: inicial.php");
}
exit;
?>
