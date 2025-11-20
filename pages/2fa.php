<?php
session_start();
if(!isset($_SESSION['user_temp'])){
    header("Location: login.php");
    exit;
}

$perguntas = [
    'nome_materno' => 'Qual o nome da sua mãe?',
    'data_nasc'    => 'Qual a sua data de nascimento? (AAAA-MM-DD)',
    'endereco'     => 'Insira seu endereço:'
];

if(!isset($_SESSION['campo2fa']) || ($_SESSION['tentativas_2fa'] ?? 0) > 0){
    $keys = array_keys($perguntas);
    $_SESSION['campo2fa'] = $keys[array_rand($keys)];
}

$campo = $_SESSION['campo2fa'];
$texto = $perguntas[$campo];
$t = $_SESSION['tentativas_2fa'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verificação 2FA - Raízes do Café</title>
  <link rel="stylesheet" href="../css/2fa.css">
</head>
<body>

  <header class="header">
    <div class="logo">
      <img src="../img/logo.png" alt="Logo Raízes do Café">
    </div>

    <nav class="navbar">
      <a href="inicio.html">Home</a>
      <a href="menucomum.php">Menu</a>
      <a href="cadastro.php">Cadastre-se</a>
    </nav>
  </header>

  <main class="container">
    <form action="../auth/valida_2fa.php" method="POST" class="form-2fa">
      <label for="resposta"><?php echo strtoupper($texto); ?></label>
      <input type="text" id="resposta" name="resposta" required autofocus>
      <button type="submit">ENVIAR</button>

      <?php if(isset($_GET['erro'])): ?>
        <p class="erro">Resposta incorreta. Tentativas: <?php echo $t; ?>/3</p>
      <?php endif; ?>
    </form>
  </main>

</body>
</html>