<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['perfil'] !== 'master'){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id'])){
    header("Location: consulta_usuarios.php");
    exit;
}

$id = $_GET['id'];

$sql = "SELECT * FROM users WHERE id = ? AND perfil = 'comum'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows === 0){
    header("Location: consulta_usuarios.php");
    exit;
}

$u = $res->fetch_assoc();
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

    $sql_up = "UPDATE users SET nome=?, email=?, cpf=?, telefone=?, data_nasc=?, nome_materno=?, sexo=?, endereco=?, login=? WHERE id=?";
    $stmt_up = $conn->prepare($sql_up);
    $stmt_up->bind_param("sssssssssi", $nome, $email, $cpf, $telefone, $data_nasc, $nome_materno, $sexo, $endereco, $login, $id);

    if($stmt_up->execute()){
        $mensagem = "Usuário atualizado com sucesso.";
    } else {
        $mensagem = "Erro ao atualizar usuário.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Usuário</title>
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
    margin-bottom: 12px;
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
    <img src="../img/logo.png">
  </div>
  <nav class="navbar">
    <a href="consulta_usuarios.php">Consulta</a>
    <a href="../auth/logout.php">Sair</a>
  </nav>
</header>

<main class="container">

<div class="form-container">

<h2>Editar Usuário</h2>

<div class="mensagem"><?php echo $mensagem; ?></div>

<form method="POST">

<input type="text" name="nome" value="<?php echo $u['nome']; ?>" required>

<input type="email" name="email" value="<?php echo $u['email']; ?>" required>

<input type="text" name="cpf" value="<?php echo $u['cpf']; ?>" required>

<input type="text" name="telefone" value="<?php echo $u['telefone']; ?>" required>

<input type="date" name="data_nasc" value="<?php echo $u['data_nasc']; ?>" required>

<input type="text" name="nome_materno" value="<?php echo $u['nome_materno']; ?>" required>

<select name="sexo" required>
    <option <?php if($u['sexo']=="Feminino") echo "selected"; ?>>Feminino</option>
    <option <?php if($u['sexo']=="Masculino") echo "selected"; ?>>Masculino</option>
    <option <?php if($u['sexo']=="Não informado") echo "selected"; ?>>Não informado</option>
</select>

<input type="text" name="endereco" value="<?php echo $u['endereco']; ?>" required>

<input type="text" name="login" value="<?php echo $u['login']; ?>" required>

<button type="submit">Salvar Alterações</button>

</form>

<div class="voltar">
    <a href="consulta_usuarios.php">Voltar</a>
</div>

</div>

</main>

</body>
</html>