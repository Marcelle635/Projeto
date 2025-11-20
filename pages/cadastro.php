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
            $mensagem = "Cadastro realizado com sucesso!";
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
        <div>
            <br><h1>CADASTRE-SE</h1>
        </div>

        <section>
            <?php if($mensagem): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <?php if($erro): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
                    <?php echo $erro; ?>
                </div>
            <?php endif; ?>

            <form id="form" method="POST" action="cadastro.php">
                <div>
                    <label for="nome"></label>
                    <input type="text" id="nome" name="nome" placeholder="NOME COMPLETO:" required>
                    <span id="span-nome-required" class="span-required">mínimo 15 caracteres e no máximo 80</span>
                </div>

                <div>
                    <label for="data_nascimento"></label>
                    <input type="date" id="data_nascimento" name="data_nascimento" required>
                </div>

                <div>
                    <label for="sexo"></label>
                    <select id="sexo" name="sexo" class="select" required>
                        <option value="">Selecione o sexo</option>
                        <option value="Feminino">Feminino</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Prefiro não dizer">Prefiro não dizer</option>
                    </select>
                </div>

                <div>
                    <label for="nome_materno"></label>
                    <input type="text" id="nome_materno" name="nome_materno" placeholder="NOME MATERNO:" required>
                </div>

                <div>
                    <label for="cpf"></label>
                    <input type="text" id="cpf" name="cpf" placeholder="CPF:" required maxlength="14"> 
                    <span id="span-required" class="span-required">somente números ou formato 999.999.999-99</span>
                </div>

                <div>
                    <label for="email"></label>
                    <input type="email" id="email" name="email" placeholder="E-MAIL:" required>
                </div>

                <div>
                    <label for="telefone"></label>
                    <input type="text" id="telefone" name="telefone" placeholder="TELEFONE CELULAR: (XX) XXXXX-XXXX" required maxlength="15">
                    <span id="span-telefone-required" class="span-required">Número de telefone inválido.</span>
                </div>

                <div class="full-width">
                    <label for="endereco"></label>
                    <input type="text" id="endereco" name="endereco" placeholder="ENDEREÇO COMPLETO:" required>
                </div>

                <div>
                    <label for="login"></label>
                    <input type="text" id="login" name="login" placeholder="LOGIN:" required>
                    <span id="span-required" class="span-required">deve ter 6 caracteres alfabéticos</span>
                </div>

                <div>
                    <label for="senha"></label>
                    <input type="password" id="senha" name="senha" placeholder="SENHA:" required>
                    <span class="span-required">deve ter 6 caracteres</span>
                </div>

                <div>
                    <label for="confirme"></label>
                    <input type="password" id="confirme" name="confirme_senha" placeholder="CONFIRME A SENHA:" required>
                    <span class="span-required">senhas devem ser iguais</span>
                </div>

                <div class="botao">
                    <button type="submit">ENVIAR</button>
                    <button type="button" id="btn-limpar">LIMPAR TELA</button>
                </div>
            </form>
        </section>
    </main>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let tamanhoFonte = localStorage.getItem("tamanhoFonte")
        ? parseInt(localStorage.getItem("tamanhoFonte"))
        : 70;

    const html = document.documentElement;
    html.style.fontSize = tamanhoFonte + "%";

    document.getElementById("aumentar-fonte").addEventListener("click", function() {
        if (tamanhoFonte < 70) {
            tamanhoFonte += 10;
            html.style.fontSize = tamanhoFonte + "%";
            localStorage.setItem("tamanhoFonte", tamanhoFonte);
        }
    });

    document.getElementById("diminuir-fonte").addEventListener("click", function() {
        if (tamanhoFonte > 40) {
            tamanhoFonte -= 10;
            html.style.fontSize = tamanhoFonte + "%";
            localStorage.setItem("tamanhoFonte", tamanhoFonte);
        }
    });

    document.getElementById("btn-limpar").addEventListener("click", function() {
        document.getElementById("form").reset();
    });
});
</script>
</body>
</html>