<?php
session_start();
require_once("../config/db.php");

$mensagem = "";
$erro = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $data_nasc = $_POST['data_nascimento'] ?? '';
    $nome_materno = $_POST['nome_materno'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirme_senha = $_POST['confirme_senha'] ?? '';
    $endereco = $_POST['endereco'] ?? '';

    if($senha !== $confirme_senha){
        $erro = "As senhas não coincidem.";
    } elseif(strlen($senha) < 6){
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (nome, email, cpf, telefone, data_nasc, nome_materno, sexo, endereco, login, senha, perfil) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'comum')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $nome, $email, $cpf, $telefone, $data_nasc, $nome_materno, $sexo, $endereco, $login, $senha_hash);
        
        if($stmt->execute()){
            header("Location: login.php");
            exit();
        } else {
            $erro = "Erro ao cadastrar usuário. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raízes do café</title>
    <link rel="stylesheet" href="../css/cadastro.css">
</head>
<body>
    <header class="header">
        <section>
            <div class="left-side">
                <a href="#" class="logo">
                    <img src="../img/logo.png" alt="logo">
                </a>
                <nav class="navbar">
                    <a href="inicio.html">Home</a>
                    <a href="menuincio.html">Menu</a>
                </nav>
            </div>

            <div class="areas">
                <a href="login.php">Login</a> |
                <button id="aumentar-fonte">A+</button>
                <button id="diminuir-fonte">A-</button>
            </div>
        </section>
    </header>

      <main>
        <h1>CADASTRE-SE</h1>

        <?php if($mensagem): ?>
            <div class="mensagem sucesso"><?= $mensagem; ?></div>
        <?php endif; ?>
        <?php if($erro): ?>
            <div class="mensagem erro"><?= $erro; ?></div>
        <?php endif; ?>

        <form id="form" method="POST" action="cadastro.php">
            <input type="text" name="nome" placeholder="NOME COMPLETO:" required>
            <input type="date" name="data_nascimento" required>
            <input type="text" name="cpf" placeholder="CPF:" required maxlength="14">
            <input type="text" name="nome_materno" placeholder="NOME MATERNO:" required>
            <input type="text" name="telefone" placeholder="TELEFONE CELULAR: (XX) XXXXX-XXXX" required maxlength="15">
            <input type="text" name="login" placeholder="LOGIN:" required>
            <select name="sexo" required>
                <option value="">SEXO:</option>
                <option value="Feminino">Feminino</option>
                <option value="Masculino">Masculino</option>
                <option value="Prefiro não dizer">Prefiro não dizer</option>
            </select>
            <input type="password" name="senha" placeholder="SENHA:" required>
            <input type="email" name="email" placeholder="E-MAIL:" required>
            <input type="password" name="confirme_senha" placeholder="CONFIRME A SENHA:" required>
            <input type="text" name="endereco" placeholder="ENDEREÇO COMPLETO:" class="full-width" required>

            <div class="botao">
                <button type="submit">ENVIAR</button>
                <button type="button" id="btn-limpar">LIMPAR TELA</button>
            </div>
        </form>
    </main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const html = document.documentElement;
    let tamanho = parseInt(localStorage.getItem("tamanhoFonte")) || 70;
    html.style.fontSize = tamanho + "%";

    document.getElementById("aumentar-fonte").onclick = () => {
        if (tamanho < 90) { tamanho += 10; html.style.fontSize = tamanho + "%"; localStorage.setItem("tamanhoFonte", tamanho); }
    };
    document.getElementById("diminuir-fonte").onclick = () => {
        if (tamanho > 50) { tamanho -= 10; html.style.fontSize = tamanho + "%"; localStorage.setItem("tamanhoFonte", tamanho); }
    };
    document.getElementById("btn-limpar").onclick = () => document.getElementById("form").reset();
});
</script>
</body>

</html>
