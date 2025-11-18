<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['perfil'] !== 'master'){
    header("Location: login.php");
    exit;
}

$mensagem = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $data_nasc = $_POST['data_nasc'];
    $nome_materno = $_POST['nome_materno'];
    $sexo = $_POST['sexo'];
    $endereco = $_POST['endereco'];
    $login = $_POST['login'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (nome, email, cpf, telefone, data_nasc, nome_materno, sexo, endereco, login, senha, perfil)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'comum')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $nome, $email, $cpf, $telefone, $data_nasc, $nome_materno, $sexo, $endereco, $login, $senha);

    if($stmt->execute()){
        $mensagem = "Usuário cadastrado com sucesso.";
    } else {
        $mensagem = "Erro ao cadastrar usuário.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Inserir Usuário</title>
<link rel="stylesheet" href="../css/consulta_usuarios.css">
<style>
.form-container {
    background: var(--branco-transp);
    width: 450px;
    margin: 40px auto;
    padding: 25px;
    border-radius: 12px;
}
.form-container h2 {
    text-align: center;
    margin-bottom: 20px;
}
.form-container input, .form-container select {
    width: 100%;
    padding: 10px;
    border: 2px solid var(--marrom);
    border-radius: 8px;
    margin-bottom: 12px;
}
.form-container button {
    width: 100%;
    background: var(--marrom);
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}
.form-container button:hover {
    background: var(--marrom-claro);
}
.mensagem {
    text-align: center;
    margin-bottom: 10px;
    font-weight: 600;
}
.voltar {
    text-align: center;
    margin-top: 20px;
}
.voltar a {
    color: var(--marrom);
    font-weight: 600;
    text-decoration: none;
}
</style>
</head>
<body>

<header class="header">
  <div class="logo">
    <img src="../img/logo.png" alt="Logo Raízes do Café">
  </div>
  <nav class="navbar">
    <a href="painel_master.php">Painel</a>
    <a href="consulta_usuarios.php">Consulta</a>
    <a href="../auth/logout.php">Sair</a>
  </nav>
</header>

<main class="container">

<div class="form-container">

<h2>Inserir Novo Usuário</h2>

<div class="mensagem"><?php echo $mensagem; ?></div>

<form method="POST">

<input type="text" name="nome" placeholder="Nome completo" required>

<input type="email" name="email" placeholder="Email" required>

<input type="text" name="cpf" placeholder="CPF" required>

<input type="text" name="telefone" placeholder="Telefone" required>

<input type="date" name="data_nasc" required>

<input type="text" name="nome_materno" placeholder="Nome materno" required>

<select name="sexo" required>
    <option value="">Selecione o sexo</option>
    <option value="Feminino">Feminino</option>
    <option value="Masculino">Masculino</option>
    <option value="Não informado">Não informado</option>
</select>

<input type="text" name="endereco" placeholder="Endereço completo" required>

<input type="text" name="login" placeholder="Login" required>

<input type="password" name="senha" placeholder="Senha" required>

<button type="submit">Cadastrar Usuário</button>

</form>

<div class="voltar">
    <a href="consulta_usuarios.php">Voltar</a>
</div>

</div>

</main>

</body>
</html>