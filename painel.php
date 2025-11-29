<?php
session_start();
include("conexao.php");

if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    header("Location: inicial.php");
    exit;
}

$sql_grafico = "SELECT p.nomeprod, SUM(i.quantidade) as total 
                FROM itens_pedido i 
                JOIN produtos p ON i.id_prod = p.id_prod 
                GROUP BY p.id_prod 
                ORDER BY total DESC LIMIT 5";
$res = $conexao->query($sql_grafico);

$labels = [];
$data = [];
while ($row = $res->fetch_assoc()) {
    $labels[] = $row['nomeprod']; 
    $data[] = $row['total'];
}

$sql_fat = "SELECT SUM(valor_total) as total FROM pedidos";
$res_fat = $conexao->query($sql_fat);
$faturamento = $res_fat->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Gygabite Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Roboto, sans-serif;
            transition: background 0.3s, color 0.3s;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container.flex-grow-1 {
            flex: 1;
        }

        .box-painel {
            background-color: #1e1e1e;
            padding: 25px;
            border-radius: 10px;
            border: 1px solid #444;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .main-footer {
            background-color: #0d6efd;
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

        .social-icons a {
            color: white;
            font-size: 1.5rem;
            margin: 0 10px;
        }

        body.light-mode {
            background-color: #f4f4f4;
            color: #333;
        }

        body.light-mode .box-painel {
            background-color: #fff;
            border-color: #ddd;
            color: #333;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body.light-mode .text-white {
            color: #333 !important;
        }

        body.light-mode .main-footer {
            border-top: 1px solid #ccc;
        }

        p{
            color: #ffffff;
        }

    </style>
</head>

<body>

    <div class="container mt-5 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-white">Painel Administrativo</h1>
            <a href="inicial.php" class="btn btn-outline-secondary">Voltar para Loja</a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="box-painel">
                    <h3 class="mb-4 text-white"><i class="fas fa-chart-bar text-primary"></i> Produtos Mais Vendidos</h3>
                    <canvas id="graficoVendas"></canvas>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="box-painel text-center">
                    <h4 class="text-white mb-3">Faturamento Total</h4>
                    <h2 class="text-success fw-bold">R$ <?php echo number_format($faturamento, 2, ',', '.'); ?></h2>
                    <p class="text-muted" style="color: #ffffff !important;">Valor bruto de vendas</p>
                </div>

                <div class="box-painel text-center">
                    <h4 class="text-white mb-3">Relatório de Vendas</h4>
                    <p class="mb-4">Baixe o relatório completo em formato PDF.</p>
                    <a href="gerar_pdf.php" target="_blank" class="btn btn-danger btn-lg w-100 fw-bold">
                        <i class="fas fa-file-pdf"></i> Gerar PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h4>Gygabite Shop</h4>
                    <p>Painel de Controle</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h4>Acesso Rápido</h4>
                    <ul class="list-unstyled">
                        <li><a href="inicial.php" class="footer-link">Ir para Loja</a></li>
                        <li><a href="logout.php" class="footer-link">Sair do Sistema</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h4>Siga-nos</h4>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                    <p class="mt-2 small">&copy; 2025 Gygabite Shop</p>
                </div>
            </div>
        </div>
    </footer>

    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 8px;">
        <button id="toggle-theme" class="btn btn-dark border-light btn-sm shadow">🌓 Tema</button>
    </div>

    <script>
        const ctx = document.getElementById('graficoVendas').getContext('2d');
        const grafico = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: <?php echo json_encode($data); ?>,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: 'grey'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'grey'
                        },
                        grid: {
                            color: 'rgba(255,255,255,0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'grey'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        const body = document.body;
        const btnTheme = document.getElementById('toggle-theme');

        function updateChartColors(isLight) {
            const color = isLight ? '#333' : '#ccc';
            const gridColor = isLight ? 'rgba(0,0,0,0.1)' : 'rgba(255,255,255,0.1)';

            grafico.options.plugins.legend.labels.color = color;
            grafico.options.scales.x.ticks.color = color;
            grafico.options.scales.y.ticks.color = color;
            grafico.options.scales.y.grid.color = gridColor;
            grafico.update();
        }

        if (localStorage.getItem('theme') === 'light') {
            body.classList.add('light-mode');
            updateChartColors(true);
        } else {
            updateChartColors(false);
        }

        btnTheme.addEventListener('click', () => {
            body.classList.toggle('light-mode');
            const isLight = body.classList.contains('light-mode');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
            updateChartColors(isLight);
        });
    </script>
</body>

</html>
