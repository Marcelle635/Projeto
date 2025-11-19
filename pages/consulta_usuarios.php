<?php
session_start();
require_once("../config/db.php");

if (!isset($_SESSION['user']) || $_SESSION['user']['perfil'] !== 'master') {
    header("Location: login.php");
    exit;
}

$busca = $_GET['busca'] ?? '';

$sql = "SELECT id, nome, email, cpf, telefone, data_nasc, nome_materno, sexo, endereco 
        FROM users 
        WHERE perfil = 'comum' AND nome LIKE ?";

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



<body class="modo-escuro">
 <!--Menu com funcionalidades-->
    <header class="header">
  <section>
    <!-- Lado esquerdo: Logo + Navegação -->
    <div class="left-side">
      <a href="#" class="logo">
        <img src="../img/logo.png" alt="logo">
      </a>

      <nav class="navbar">
        <a href="../masterhome.html">Home</a>
        <a href="../mastermenu.html">Menu</a>
        <a href="consulta_usuarios.php">Consulta de Usuários</a>
      </nav>
    
        
       
    </div>
<!-- Lado direito: Botões e links -->
    <div class="areas">
      
    <!--trilho e indicador pra botar o escuro e claro-->
      <div class="trilho">
        <div class="indicador"></div>
      </div> <!-- BOTÃO DESLIZANTE -->
<label class="toggle-switch" title="Alternar modo escuro/claro">
  <input type="checkbox" id="toggle-contraste">
  <span class="slider"></span>
</label>  |
 <div class="areas">
      <button id="aumentar-fonte">A+</button>
      <button id="diminuir-fonte">A-</button> | 
   
    <?php
$usuarioLogado = $_SESSION['user']['nome'];
?>
<!-- Menu do usuário -->
<div class="menu-usuario">
    <span class="usuario-nome"> Bem-vindo, <strong><?php echo htmlspecialchars($usuarioLogado); ?></strong></span>
    <i class="arrow"></i>

    <div class="dropdown">
        <a href="../auth/logout.php" class="logout-btn">Sair</a>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const menu = document.querySelector(".menu-usuario");

    menu.addEventListener("click", () => {
        menu.classList.toggle("active");
    });

    // Fechar dropdown ao clicar fora
    document.addEventListener("click", (e) => {
        if (!menu.contains(e.target)) {
            menu.classList.remove("active");
        }
    });
});
</script>


</div>
    </div>
  </section>
</header>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById("toggle-contraste");
  const consulta = document.querySelector("main.container");

  // Debug: checagens iniciais
  if (!toggle) {
    console.error("[MODO-ESC] toggle-contraste NÃO encontrado (id). Verifique se existe apenas UM elemento com esse id.");
    return;
  } else {
    console.log("[MODO-ESC] toggle encontrado.");
  }

  if (!consulta) {
    console.error("[MODO-ESC] área 'main.container' NÃO encontrada.");
    return;
  } else {
    console.log("[MODO-ESC] área da consulta encontrada.");
  }

  // Restaurar estado salvo (opcional)
  const estadoSalvo = localStorage.getItem("escuroConsulta");
  if (estadoSalvo === "true") {
    toggle.checked = true;
    consulta.classList.add("escuro-consulta");
    console.log("[MODO-ESC] restaurado: ativo");
  } else {
    toggle.checked = false;
    consulta.classList.remove("escuro-consulta");
    console.log("[MODO-ESC] restaurado: inativo");
  }

  // Event listener
  toggle.addEventListener("change", function () {
    const ativo = !!this.checked;
    consulta.classList.toggle("escuro-consulta", ativo);
    localStorage.setItem("escuroConsulta", ativo ? "true" : "false");
    console.log("[MODO-ESC] toggle mudou para:", ativo);
  });

  // Extra: clique na label também deve mudar (por padrão já muda, mas só pra garantir)
  const label = toggle.closest("label");
  if (label) {
    label.addEventListener("click", function (e) {
      // delay curto para deixar o checkbox atualizar antes de aplicar a classe
      setTimeout(() => {
        const ativo = !!toggle.checked;
        consulta.classList.toggle("escuro-consulta", ativo);
        localStorage.setItem("escuroConsulta", ativo ? "true" : "false");
        console.log("[MODO-ESC] clique na label -> ativo:", ativo);
      }, 10);
    });
  }
});
</script>


<script>

  // === FONTE A+/A- (mantenha isso) ===
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

</script>

<!--Imagem do café-->

    <div class="home-container">
        <section id="home">
          <div class="content">
            <h3>O MELHOR CAFÉ DA REGIÃO</h3>
            <p>Cada xícara é um convite para desacelerar e saborear o que<br> há de melhor. Trabalhamos com grãos selecionados de origem<br> brasileira, preparados com carinho e atenção aos detalhes. </p>
            <a href="#" class="btn">Pegue o seu agora</a>
          </div>
        </section>
    </div>

 
   <main class="container">
    <h1 class="title">Consulta de Usuários</h1>

    <form method="GET" class="busca-form">
        <input 
            type="text" 
            name="busca" 
            placeholder="Pesquisar por nome..." 
            value="<?php echo htmlspecialchars($busca); ?>"
        >
        <button type="submit">Buscar</button>
        <a href="inserir_usuario.php" class="btn-novo">Adicionar Usuário</a>
    </form>

    <div class="tabela-container">
        <table class="tabela">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Data Nasc.</th>
                    <th>Nome Materno</th>
                    <th>Sexo</th>
                    <th>Endereço</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['cpf']); ?></td>
                    <td><?php echo htmlspecialchars($row['telefone']); ?></td>
                    <td><?php echo htmlspecialchars($row['data_nasc']); ?></td>
                    <td><?php echo htmlspecialchars($row['nome_materno']); ?></td>
                    <td><?php echo htmlspecialchars($row['sexo']); ?></td>
                    <td><?php echo htmlspecialchars($row['endereco']); ?></td>

                    <td class="acoes">
                        <a href="editar_usuario.php?id=<?php echo $row['id']; ?>" class="btn-editar">Editar</a>
                        <form 
                            method="POST" 
                            action="../auth/excluir_usuario.php" 
                            class="form-excluir"
                            onsubmit="return confirm('Deseja realmente excluir este usuário?');"
                        >
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn-excluir">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>
 



  <!-- RODAPÉ -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-col">
        <img src="../img/logo clara.jpeg" alt="Raízes do Café" class="footer-logo">
        <p>
          Raízes do café é a extensão<br> da sua casa. A nossa casa<br> existe para compartilhar
          o sabor<br> dos bons momentos com você e sua família.
        </p>
      </div>

      <div class="footer-col">
        <h3>POSTS RECENTES</h3>
      </div>

      <div class="footer-col">
        <h3>NOSSAS LOJAS</h3>
        <ul>
          <li>Copacabana</li>
          <li>Rio Sul</li>
          <li>Barra da Tijuca</li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>RAÍZES 2025 MARKETING DIGITAL E PERFORMANCE <a href="#">360R.</a></p>
      <div class="payment-icons">
        <img src="../img/formas-de-pagamento 1.png" alt="Formas de pagamento">
      </div>
    </div>
  </footer>
    </body>
</html>
