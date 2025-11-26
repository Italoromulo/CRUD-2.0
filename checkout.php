<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$total_itens_carrinho = 0;
if (isset($_SESSION['carrinho'])) {
    $total_itens_carrinho = array_sum($_SESSION['carrinho']);
}

$total_geral = 0;
$isAdmin = isset($_SESSION['adm']) && $_SESSION['adm'] == 1;

if (!empty($_SESSION['carrinho'])) {
    $ids = implode(',', array_keys($_SESSION['carrinho']));
    if (!empty($ids)) {
        $sql = "SELECT * FROM produtos WHERE id_prod IN ($ids)";
        $result = $conexao->query($sql);
        while ($row = $result->fetch_assoc()) {
            $total_geral += $row['preco'] * $_SESSION['carrinho'][$row['id_prod']];
        }
    }
}

$compra_sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['id_usuario'];

    $metodo = isset($_POST['metodo_pagamento']) ? $_POST['metodo_pagamento'] : 'Indefinido';
    $rua = isset($_POST['endereco']) ? $_POST['endereco'] : '';
    $cidade = isset($_POST['cidade']) ? $_POST['cidade'] : '';
    $cep = isset($_POST['cep']) ? $_POST['cep'] : '';
    $endereco_completo = "$rua, $cidade - CEP: $cep";

    $sql_pedido = "INSERT INTO pedidos (id_usuario, valor_total, endereco_entrega, forma_pagamento, status, prazo_entrega) 
                   VALUES (?, ?, ?, ?, 'Pendente', 'de 10 a 30 dias úteis')";

    $stmt = $conexao->prepare($sql_pedido);
    $stmt->bind_param("idss", $id_usuario, $total_geral, $endereco_completo, $metodo);

    if ($stmt->execute()) {
        $id_pedido_novo = $conexao->insert_id;

        if (!empty($_SESSION['carrinho'])) {
            $ids = implode(',', array_keys($_SESSION['carrinho']));

            $sql_prods = "SELECT id_prod, preco FROM produtos WHERE id_prod IN ($ids)";
            $res_prods = $conexao->query($sql_prods);

            while ($prod = $res_prods->fetch_assoc()) {
                $id_produto = $prod['id_prod'];
                $qtd = $_SESSION['carrinho'][$id_produto];
                $preco_unitario = $prod['preco'];

                $sql_item = "INSERT INTO itens_pedido (id_pedido, id_prod, quantidade, preco_unitario) 
                             VALUES ('$id_pedido_novo', '$id_produto', '$qtd', '$preco_unitario')";
                $conexao->query($sql_item);
            }
        }

        unset($_SESSION['carrinho']);
        $compra_sucesso = true;

        $total_geral = 0;
        $total_itens_carrinho = 0;
    } else {
        echo "<script>alert('Erro ao processar pedido: " . $conexao->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Checkout - Gygabite Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        /* MANTIDO SEU CSS ORIGINAL */
        :root {
            --primary-color: #0d6efd;
            --card-bg: #1e1e1e;
        }

        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Roboto, sans-serif;
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

        .main-header p,
        input,
        label,
        h4,
        .btn {
            color: #ccc !important;
        }

        ::placeholder {
            color: #ccc !important;
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

        .checkout-box {
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #444;
        }

        .form-control {
            background-color: #2b2b2b;
            color: #fff;
            border: 1px solid #555;
        }

        .form-control:focus {
            background-color: #333;
            color: #fff;
            border-color: #0d6efd;
        }

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

        .parcela-option {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border: 1px solid #444;
            border-radius: 5px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .parcela-option small {
            color: #ccc !important;
        }

        .parcela-option:hover {
            background-color: #2b2b2b;
        }

        .parcela-radio:checked+.parcela-content {
            color: #0d6efd !important;
            font-weight: bold;
        }

        .parcela-radio {
            display: none;
        }

        .text-muted-small {
            font-size: 0.85rem;
            color: #888;
        }

        .payment-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .payment-btn {
            flex: 1;
            padding: 15px;
            background-color: #2b2b2b;
            border: 2px solid #444;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #ccc;
        }

        .payment-btn:hover {
            border-color: #666;
        }

        input[name="metodo_pagamento"]:checked+.payment-btn {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
            color: white;
            font-weight: bold;
        }

        .payment-content {
            display: none;
        }

        .payment-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <header class="main-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="inicial.php" class="logo">Gygabite <span>shop</span></a>
            <nav class="d-none d-lg-flex">
                <a href="inicial.php" class="nav-link-custom">Início</a>
            </nav>
            <div class="d-flex align-items-center gap-3">
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

    <div class="container">

        <?php if ($compra_sucesso): ?>
            <div class="checkout-box text-center mt-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                </div>
                <h2 class="text-white">Pedido Confirmado!</h2>
                <p class="lead mb-4 text-white-50">Obrigado, <?php echo htmlspecialchars($_SESSION['login']); ?>. Sua compra foi realizada com sucesso.</p>
                <a href="inicial.php" class="btn btn-primary btn-lg">Voltar para a Loja</a>
            </div>

        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="checkout-box mb-4">
                        <h4 class="mb-4 text-primary"><i class="fas fa-map-marker-alt"></i> Endereço de Entrega</h4>
                        <form method="POST" action="checkout.php">

                            <div class="row g-3 mb-5">
                                <div class="col-12">
                                    <label class="form-label text-white">Endereço Completo</label>
                                    <input type="text" name="endereco" class="form-control" required placeholder="Rua, Número, Bairro">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white">Cidade</label>
                                    <input type="text" name="cidade" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-white">CEP</label>
                                    <input type="text" name="cep" class="form-control" id="cep" required placeholder="00000-000" maxlength="9" oninput="mascaraCEP(this)">
                                </div>
                            </div>

                            <h4 class="mb-4 text-primary"><i class="far fa-credit-card"></i> Pagamento</h4>

                            <div class="payment-selector">
                                <label style="flex:1;">
                                    <input type="radio" name="metodo_pagamento" value="cartao" checked hidden onchange="togglePayment('cartao')">
                                    <div class="payment-btn">
                                        <i class="far fa-credit-card fa-lg mb-2"></i><br>Cartão
                                    </div>
                                </label>
                                <label style="flex:1;">
                                    <input type="radio" name="metodo_pagamento" value="pix" hidden onchange="togglePayment('pix')">
                                    <div class="payment-btn">
                                        <i class="fas fa-qrcode fa-lg mb-2"></i><br>Pix
                                    </div>
                                </label>
                                <label style="flex:1;">
                                    <input type="radio" name="metodo_pagamento" value="boleto" hidden onchange="togglePayment('boleto')">
                                    <div class="payment-btn">
                                        <i class="fas fa-barcode fa-lg mb-2"></i><br>Boleto
                                    </div>
                                </label>
                            </div>

                            <div id="area-cartao" class="payment-content active">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label text-white">Nome no Cartão</label>
                                        <input type="text" class="form-control" placeholder="Como impresso no cartão" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-white">Número do Cartão</label>
                                        <input type="text" class="form-control" id="cartao" placeholder="0000 0000 0000 0000" maxlength="19" oninput="mascaraCartao(this)" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-white">Validade</label>
                                        <input type="text" class="form-control" id="validade" placeholder="MM/AA" maxlength="5" oninput="mascaraData(this)" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-white">CVV</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="123" maxlength="3" oninput="mascaraCVV(this)" required>
                                    </div>
                                </div>

                                <h4 class="mb-3 mt-5 text-primary"><i class="fas fa-list-ol"></i> Parcelamento</h4>
                                <div class="parcelas-container">
                                    <?php
                                    $maxSemJuros = 6;
                                    $maxParcelas = 12;

                                    for ($i = 1; $i <= $maxParcelas; $i++) {
                                        $textoJuros = "sem juros";

                                        if ($i <= $maxSemJuros) {
                                            $valorParcela = $total_geral / $i;
                                            $valorTotalFinal = $total_geral;
                                        } else {
                                            $taxa = 3 + (($i - 7) * 0.5);
                                            $valorTotalFinal = $total_geral * (1 + ($taxa / 100));
                                            $valorParcela = $valorTotalFinal / $i;
                                            $textoJuros = "com juros";
                                        }

                                        $check = ($i == 1) ? "checked" : "";

                                        echo "
                                        <label class='parcela-option'>
                                            <div style='display:flex; align-items:center; gap:10px;'>
                                                <input type='radio' name='parcela' value='$i' class='parcela-radio' $check 
                                                    onclick='atualizarTotal(" . json_encode($valorTotalFinal) . ")'>
                                                <span class='parcela-content'>{$i}x de R$ " . number_format($valorParcela, 2, ',', '.') . "</span>
                                            </div>
                                            <small class='text-muted'>$textoJuros</small>
                                        </label>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div id="area-pix" class="payment-content text-center p-4">
                                <i class="fas fa-qrcode fa-5x text-white mb-3"></i>
                                <p class="text-white">Ao finalizar a compra, um QR Code será gerado.</p>
                                <div class="alert alert-success">
                                    <strong>10% de desconto</strong> pagando via Pix!
                                </div>
                            </div>

                            <div id="area-boleto" class="payment-content text-center p-4">
                                <i class="fas fa-barcode fa-5x text-white mb-3"></i>
                                <p class="text-white">O boleto será gerado após a confirmação.</p>
                                <div class="alert alert-warning">
                                    Vencimento em <strong>3 dias úteis</strong>.
                                </div>
                            </div>

                            <hr class="my-4 border-secondary">
                            <button class="btn btn-success w-100 btn-lg fw-bold" type="submit">
                                Confirmar Compra
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="checkout-box position-sticky" style="top: 100px;">
                        <h4 class="d-flex justify-content-between align-items-center mb-3 text-white">
                            <span class="text-primary">Resumo</span>
                            <span class="badge bg-primary rounded-pill"><?php echo $total_itens_carrinho; ?> itens</span>
                        </h4>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between bg-dark text-white border-secondary">
                                <span>Total (BRL)</span>
                                <strong id="valor-total-display">R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></strong>
                            </li>
                        </ul>
                        <a href="carrinho.php" class="btn btn-outline-light w-100 mt-2">Voltar ao Carrinho</a>
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
                    <p>Sua loja de confiança.</p>
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
    <script>
        function togglePayment(method) {
            document.getElementById('area-cartao').classList.remove('active');
            document.getElementById('area-pix').classList.remove('active');
            document.getElementById('area-boleto').classList.remove('active');
            document.getElementById('area-' + method).classList.add('active');

            // Só mexe no total se não for cartão (lógica original)
            if (method !== 'cartao') {
                const totalOriginal = <?php echo $total_geral; ?>;
                const valorFormatado = new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }).format(totalOriginal);
                document.getElementById('valor-total-display').innerText = valorFormatado;
            } else {
                const radioSelecionado = document.querySelector('input[name="parcela"]:checked');
                if (radioSelecionado) {
                    radioSelecionado.click();
                }
            }
        }

        function atualizarTotal(valor) {
            const formatado = new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(valor);
            document.getElementById('valor-total-display').innerText = formatado;
        }

        function mascaraCartao(i) {
            let v = i.value.replace(/\D/g, "");
            v = v.replace(/(\d{4})/g, "$1 ");
            v = v.replace(/\.$/, "");
            v = v.substring(0, 19);
            i.value = v;
        }

        function mascaraData(i) {
            let v = i.value.replace(/\D/g, "");
            v = v.replace(/(\d{2})(\d)/, "$1/$2");
            i.value = v;
        }

        function mascaraCVV(i) {
            i.value = i.value.replace(/\D/g, "");
        }

        function mascaraCEP(i) {
            let v = i.value.replace(/\D/g, "");
            v = v.replace(/^(\d{5})(\d)/, "$1-$2");
            i.value = v;
        }
    </script>
</body>

</html>