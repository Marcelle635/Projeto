<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

if($_SESSION['user']['perfil'] !== 'comum'){
    header("Location: " . ($_SESSION['user']['perfil'] === 'master' ? 'masterhome.php' : 'menucomum.php'));
    exit;
}

$user_id = $_SESSION['user']['id'];
$mensagem = "";
$erro = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if($nova_senha !== $confirmar_senha){
        $erro = "As novas senhas não coincidem.";
    } elseif(strlen($nova_senha) < 6){
        $erro = "A nova senha deve ter pelo menos 6 caracteres.";
    } else {
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt_update = $conn->prepare("UPDATE users SET senha = ? WHERE id = ?");
        $stmt_update->bind_param("si", $nova_senha_hash, $user_id);

        if($stmt_update->execute()){
            $mensagem = "Senha alterada com sucesso!";
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            grava_log($conn, $user_id, $_SESSION['user']['login'], 'senha_alterada', 'Senha redefinida com sucesso', $ip);
        } else {
            $erro = "Erro ao alterar senha. Tente novamente.";
        }
    }
}

function grava_log($conn, $usuario_id, $login_via, $acao, $detalhe, $ip){
    $s = mysqli_prepare($conn, "INSERT INTO logs (usuario_id, login_via, acao, detalhe, ip) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($s, "issss", $usuario_id, $login_via, $acao, $detalhe, $ip);
    mysqli_stmt_execute($s);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha - Raízes do Café</title>
    <link rel="stylesheet" href="../css/alterar_senha.css">
</head>
<body>
    <header class="header">
        <div class="left-side">
            <a href="#" class="logo">
                <img src="../img/logo.png" alt="logo">
            </a>
            <nav class="navbar">
                <a href="comum.php">Home</a>
                <a href="menucomum.php">Menu</a>
            </nav>
        </div>

        <div class="areas">
            <label class="toggle-switch" title="Alternar modo escuro/claro">
                <input type="checkbox" id="toggle-contraste">
                <span class="slider"></span>
            </label> 
            <button id="aumentar-fonte">A+</button>
            <button id="diminuir-fonte">A-</button>
            <div class="menu-usuario">
                <span class="usuario-nome">
                    Bem-vindo(a), <strong><?php echo htmlspecialchars($_SESSION['user']['nome']); ?></strong>
                </span>
                <div class="arrow"></div>
                <div class="dropdown">
                    <a href="../auth/logout.php" class="logout-btn">Sair</a>
                </div>
            </div>
        </div>
    </header>

    <main class="container-alterar-senha">
        <div class="form-container-senha">
            <h2>Redefinir Senha</h2>
            <p class="descricao">Digite sua nova senha abaixo:</p>
            
            <?php if($mensagem): ?>
                <div class="mensagem sucesso"><?php echo $mensagem; ?></div>
            <?php endif; ?>
            
            <?php if($erro): ?>
                <div class="mensagem erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form method="POST" class="form-senha">
                <div class="input-group">
                    <label for="nova_senha">Nova Senha:</label>
                    <input type="password" id="nova_senha" name="nova_senha" required minlength="6" placeholder="Digite sua nova senha">
                    <small>Mínimo 6 caracteres</small>
                </div>

                <div class="input-group">
                    <label for="confirmar_senha">Confirmar Nova Senha:</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6" placeholder="Confirme sua nova senha">
                </div>

                <div class="botoes-container">
                    <button type="submit" class="btn-alterar">Redefinir Senha</button>
                    <a href="menucomum.php" class="btn-voltar">Voltar ao Menu</a>
                </div>
            </form>
        </div>
    </main>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const menu = document.querySelector(".menu-usuario");
        menu.addEventListener("click", () => {
            menu.classList.toggle("active");
        });
        document.addEventListener("click", (e) => {
            if (!menu.contains(e.target)) {
                menu.classList.remove("active");
            }
        });

        const checkbox = document.getElementById("toggle-contraste");
        const body = document.body;
        const modoSalvo = localStorage.getItem("contrasteConteudo") || "escuro";
        if (modoSalvo === "claro") {
            body.classList.add("modo-claro");
            checkbox.checked = true;
        }
        checkbox.addEventListener("change", function () {
            if (this.checked) {
                body.classList.add("modo-claro");
                localStorage.setItem("contrasteConteudo", "claro");
            } else {
                body.classList.remove("modo-claro");
                localStorage.setItem("contrasteConteudo", "escuro");
            }
        });

        let tamanhoFonte = localStorage.getItem("tamanhoFonte") ? parseInt(localStorage.getItem("tamanhoFonte")) : 70;
        const html = document.documentElement;
        html.style.fontSize = tamanhoFonte + "%";
        document.getElementById("aumentar-fonte").addEventListener("click", function () {
            if (tamanhoFonte < 70) {
                tamanhoFonte += 10;
                html.style.fontSize = tamanhoFonte + "%";
                localStorage.setItem("tamanhoFonte", tamanhoFonte);
            }
        });
        document.getElementById("diminuir-fonte").addEventListener("click", function () {
            if (tamanhoFonte > 40) {
                tamanhoFonte -= 10;
                html.style.fontSize = tamanhoFonte + "%";
                localStorage.setItem("tamanhoFonte", tamanhoFonte);
            }
        });
    });
    </script>
</body>
</html>