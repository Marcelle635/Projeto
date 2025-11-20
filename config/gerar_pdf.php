<?php
// Importar a conexão com o banco
require_once "db.php";

// Importar a biblioteca FPDF (que está na pasta fpdf/)
require_once "fpdf/fpdf.php";

// Criar um novo PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Título
$pdf->Cell(0, 10, 'Lista de Usuarios', 0, 1, 'C');
$pdf->Ln(5);

// Cabeçalho da tabela
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 8, 'ID', 1);
$pdf->Cell(40, 8, 'Nome', 1);
$pdf->Cell(25, 8, 'Nascimento', 1);
$pdf->Cell(25, 8, 'CPF', 1);
$pdf->Cell(30, 8, 'Login', 1);
$pdf->Cell(25, 8, 'Perfil', 1);
$pdf->Cell(35, 8, 'Telefone', 1);
$pdf->Ln();

// Buscar os usuários no banco
$sql = "SELECT id, nome, data_nasc, cpf, login, perfil, telefone FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

$pdf->SetFont('Arial', '', 9);

// Preencher as linhas da tabela
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(10, 8, $row['id'], 1);
    $pdf->Cell(40, 8, utf8_decode($row['nome']), 1);
    $pdf->Cell(25, 8, $row['data_nasc'], 1);
    $pdf->Cell(25, 8, $row['cpf'], 1);
    $pdf->Cell(30, 8, $row['login'], 1);
    $pdf->Cell(25, 8, $row['perfil'], 1);
    $pdf->Cell(35, 8, $row['telefone'], 1);
    $pdf->Ln();
}

// Forçar download do PDF
$pdf->Output('D', 'lista_usuarios.pdf');
?>
