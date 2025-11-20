<?php
session_start();
if(isset($_SESSION['user'])){
  if($_SESSION['user']['perfil'] === 'master'){
    header("Location: consulta_usuarios.php");
  } else {
    header("Location: menucomum.php");
  }
  exit;
}

$erro = $_GET['erro'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Raízes do Café</title>
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
  <header class="header">
    <div class="logo">
      <img src="../img/logo.png" alt="Logo Raízes do Café">
    </div>
    <nav class="navbar">
      <a href="inicio.html">Home</a>
      <a href="menucomum.html">Menu</a>
      <a href="cadastro.php">Cadastre-se</a>
    </nav>
  </header>

  <main class="login-container">
    <form class="login-form" method="POST" action="../auth/valida_login.php">
      <h1>Bem-vindo!</h1>
      <input type="text" name="login" placeholder="Login" required autofocus>
      <input type="password" name="senha" placeholder="Senha" required>
      
      <div class="botoes-container">
        <button type="submit" class="btn-entrar">Entrar</button>
        <button type="button" class="btn-limpar" onclick="limparCampos()">Limpar</button>
      </div>

      <?php if($erro === '1'): ?>
        <p class="erro">Login ou senha incorretos.</p>
      <?php elseif($erro === '3tentativas'): ?>
        <p class="erro">Muitas tentativas no 2FA. Tente novamente mais tarde.</p>
      <?php endif; ?>
    </form>
  </main>

  <script>
    function limparCampos() {
      document.querySelector('input[name="login"]').value = '';
      document.querySelector('input[name="senha"]').value = '';
      document.querySelector('input[name="login"]').focus();
    }
  </script>
</body>
</html>