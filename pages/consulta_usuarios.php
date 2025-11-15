<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['perfil'] !== 'master'){
    header("Location: login.php");
    exit;
}

$busca = $_GET['busca'] ?? '';

$sql = "SELECT id, nome, email, perfil FROM users WHERE perfil = 'comum' AND nome LIKE ?";
$stmt = $conn->prepare($sql);
$like = "%$busca%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Consulta de Usuários - Raízes do Café</title>
  <link rel="stylesheet" href="../css/consulta_usuarios.css">
</head>
<body>

<header class="header">
  <div class="logo">
    <img src="../img/logo.png" alt="Logo Raízes do Café">
  </div>
  <nav class="navbar">
    <a href="painel_master.php">Painel</a>
    <a href="consulta_usuarios.php" class="ativo">Consulta de Usuários</a>
    <a href="../auth/logout.php">Sair</a>
  </nav>
</header>

<main class="container">
  <h1>Consulta de Usuários</h1>

  <form method="GET" class="busca-form">
    <input type="text" name="busca" placeholder="Pesquisar por nome..." value="<?php echo htmlspecialchars($busca); ?>">
    <button type="submit">Buscar</button>
  </form>

  <table class="tabela">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['nome']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td>
          <form method="POST" action="../auth/excluir_usuario.php" onsubmit="return confirm('Deseja realmente excluir este usuário?');">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <button type="submit" class="btn-excluir">Excluir</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</main>
<footer class="footer">
  <div class="footer-section footer-logo">
    <img src="img/logo clara.jpeg" alt="Logo Raízes do Café">
    <p>Raízes do café é a extensão da sua casa.<br>
    A nossa casa existe para compartilhar o sabor dos bons momentos com você e sua família.</p>
  </div>

  <div class="footer-section">
    <h3>POSTS RECENTES</h3>
    <p>Raízes 2025 MARKETING DIGITAL E PERFORMANCE 360º</p>
  </div>

  <div class="footer-section">
    <h3>NOSSAS LOJAS</h3>
    <p>Copacabana</p>
    <p>Rio Sul</p>
    <p>Barra da Tijuca</p>
  </div>
</footer>

<div class="footer-bottom">
  RAÍZES 2025 MARKETING DIGITAL E PERFORMANCE 360º
</div>
</body>

</html>
