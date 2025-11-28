<?php
// Carrega a biblioteca FPDF (Verifique se a pasta 'fpdf' está no seu projeto)
require('fpdf/fpdf.php');
include("conexao.php");

session_start();

// Verifica se é administrador
if (!isset($_SESSION['adm']) || $_SESSION['adm'] != 1) {
    die("Acesso restrito. Faça login como administrador.");
}

class PDF extends FPDF
{
    // Cabeçalho do PDF
    function Header()
    {
        // Logo ou Título
        $this->SetFont('Arial', 'B', 16);
        // Título centralizado
        $this->Cell(0, 10, 'Relatorio de Vendas - Gygabite Shop', 0, 1, 'C');
        $this->Ln(5);

        // Data e Hora
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Gerado em: ' . date('d/m/Y H:i'), 0, 1, 'C');
        $this->Ln(10);

        // Cabeçalho da Tabela (Cores: Fundo Azul, Texto Branco)
        $this->SetFillColor(13, 110, 253);
        $this->SetTextColor(255);
        $this->SetFont('Arial', 'B', 10);

        // Larguras das colunas: ID, Cliente, Data, Valor
        $this->Cell(20, 10, 'ID', 1, 0, 'C', true);
        $this->Cell(80, 10, 'Cliente', 1, 0, 'L', true);
        $this->Cell(50, 10, 'Data', 1, 0, 'C', true);
        $this->Cell(40, 10, 'Valor (R$)', 1, 1, 'R', true);
    }

    // Rodapé do PDF
    function Footer()
    {
        // Posição a 1.5 cm do fim
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128); // Cinza
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

// Inicia o PDF
$pdf = new PDF();
$pdf->AliasNbPages(); // Ativa contagem de páginas
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0); // Volta texto para preto

// Consulta os pedidos no Banco de Dados
$sql = "SELECT p.id_pedido, u.nome, p.data_pedido, p.valor_total 
        FROM pedidos p 
        JOIN usuarios u ON p.id_usuario = u.id_usuario 
        ORDER BY p.data_pedido DESC";

$result = $conexao->query($sql);

$total_geral = 0;

// Loop para criar as linhas da tabela
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(20, 10, $row['id_pedido'], 1, 0, 'C');
        // utf8_decode serve para arrumar acentos (Gygabite Shop -> Gygabite Shop)
        $pdf->Cell(80, 10, utf8_decode($row['nome']), 1, 0, 'L');
        $pdf->Cell(50, 10, date('d/m/Y H:i', strtotime($row['data_pedido'])), 1, 0, 'C');
        $pdf->Cell(40, 10, number_format($row['valor_total'], 2, ',', '.'), 1, 1, 'R');

        $total_geral += $row['valor_total'];
    }
} else {
    $pdf->Cell(0, 10, 'Nenhuma venda encontrada.', 1, 1, 'C');
}

// Linha de Total Geral
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(150, 10, 'Total Geral:', 0, 0, 'R');
$pdf->SetTextColor(13, 110, 253); // Azul
$pdf->Cell(40, 10, 'R$ ' . number_format($total_geral, 2, ',', '.'), 0, 1, 'R');

// Gera o arquivo e manda para o navegador (D = Download, I = Inline/Visualizar)
$pdf->Output('I', 'Relatorio_Vendas.pdf');
