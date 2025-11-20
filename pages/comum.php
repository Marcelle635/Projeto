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
    <title>Raízes do café</title>
    <link rel="stylesheet" href="../css/comum.css">
</head>
<body>
   <!--Menu com funcionalidades-->
    <header class="header">
  <section>
    <div class="left-side">
      <a href="#" class="logo">
        <img src="../img/logo.png" alt="logo">
      </a>

      <nav class="navbar">
        <a href="../pages/comum.php">Home</a>
        <a href="../pages/menucomum.php">Menu</a>
        
      </nav>
    </div>

    <!-- Lado direito -->
    <div class="areas">

      <!-- Botão escuro/claro -->
      <label class="toggle-switch" title="Alternar modo escuro/claro">
        <input type="checkbox" id="toggle-contraste">
        <span class="slider"></span>
      </label> |

      <!-- Aumentar/diminuir fonte -->
      <button id="aumentar-fonte">A+</button>
      <button id="diminuir-fonte">A-</button> |

      <!-- Menu do usuário -->
      <?php $usuarioLogado = $_SESSION['user']['nome']; ?>

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
});
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const checkbox = document.getElementById("toggle-contraste");
    const body = document.body;

    // Carregar estado salvo
    const modoSalvo = localStorage.getItem("contrasteConteudo") || "escuro";
    if (modoSalvo === "claro") {
      body.classList.add("modo-claro");
      checkbox.checked = true;
    }

    // Alternar ao clicar
    checkbox.addEventListener("change", function () {
      if (this.checked) {
        body.classList.add("modo-claro");
        localStorage.setItem("contrasteConteudo", "claro");
      } else {
        body.classList.remove("modo-claro");
        localStorage.setItem("contrasteConteudo", "escuro");
      }
    });

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
   
  
    
       
          
         
            <!-- SOBRE NÓS -->
               <section class="about" id="about">
          <h2 class="title">Sobre Nós</h2>
          <div class="row">
              <div class="container-imagem">
                  <img src="../img/cast.png" alt="Café">
              </div>
              <div class="cont">
                  <h3>O que faz nosso café ser especial? </h3>
                  <p>A cafeteria Raízes do Café se destaca pela qualidade,<br> oferecendo grãos especiais provenientes de pequenos<br> produtores e um processo de torra artesanal que garante<br> sabores e aromas únicos. O ambiente é acolhedor e<br> familiar, perfeito para quem busca um momento de<br> relaxamento ou um espaço para reuniões. Ao consumir no<br> Raízes do Café, você não só desfruta de uma bebida<br> excepcional, mas também contribui para uma cadeia de<br> produção mais justa alinhada com valores que vão além<br> da xícara.</p>
              </div>
          </div>
             </section>
         
             <!-- FEEDBACK -->
           <section class="feedback-section">
             <h2>Feedback</h2>
             
             <div class="feedback-container">
          <div class="feedback-card">
          <h3>Marina Santos</h3>
          <p>Adorei o café, é realmente especial e com um sabor que se destaca. O bolo de chocolate perfeito!</p>
               </div>
               <div class="feedback-card">
          <h3>Caleb Vieira</h3>
          <p>O cupcake de morango é sensacional e o ambiente é super aconchegante.</p>
               </div>
               <div class="feedback-card">
          <h3>Patrícia Rocha</h3>
          <p>O café cremoso é delicioso e o atendimento faz toda diferença.</p>
               </div>
               <div class="feedback-card">
          <h3>Samuel Ferreira</h3>
          <p>O petit gateau e os chocolates são imperdíveis. O melhor café da região!</p>
               </div>
             </div>
           </section>
          </div>
        
      
      

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
