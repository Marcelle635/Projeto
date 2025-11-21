<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$usuarioLogado = $_SESSION['user']['nome'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelo do Banco de Dados - Raízes do Café</title>
    <link rel="stylesheet" href="../css/modelo_bd.css">
</head>
<body>
    <header class="header">
        <section>
            <div class="left-side">
                <a href="#" class="logo">
                    <img src="../img/logo.png" alt="logo">
                </a>
                <nav class="navbar">
                    <a href="masterhome.php">Home</a>
                    <a href="mastermenu.php">Menu</a>
                </nav>
            </div>

            <div class="areas">
                <label class="toggle-switch" title="Alternar modo escuro/claro">
                    <input type="checkbox" id="toggle-contraste">
                    <span class="slider"></span>
                </label> |

                <button id="aumentar-fonte">A+</button>
                <button id="diminuir-fonte">A-</button> |

                <div class="menu-usuario">
                    <span class="usuario-nome">
                        Bem-vindo(a), <strong><?php echo htmlspecialchars($usuarioLogado); ?></strong>
                    </span>
                    <div class="arrow"></div>
                    <div class="dropdown">
                        <a href="../auth/logout.php" class="logout-btn">Sair</a>
                    </div>
                </div>
            </div>
        </section>
    </header>

    <main>
        <div class="modelo-container">
            <h1>Modelo do Banco de Dados</h1>
            <div class="imagem-container">
                <img src="../img/modelo_bd.png" alt="Modelo do Banco de Dados" class="modelo-imagem">
            </div>
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