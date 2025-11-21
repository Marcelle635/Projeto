<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['perfil'] !== 'master'){
    header("Location: login.php");
    exit;
}

$busca = $_GET['busca'] ?? '';
$filtro = $_GET['filtro'] ?? 'todos';
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = 20;
$offset = ($pagina - 1) * $limite;

$where = "WHERE (l.acao LIKE '%2fa%' OR l.acao LIKE '%login%')";
$params = [];
$types = "";

if(!empty($busca)){
    if($filtro === 'nome'){
        $where .= " AND u.nome LIKE ?";
        $like = "%$busca%";
        $params = [$like];
        $types = "s";
    } elseif($filtro === 'cpf'){
        $where .= " AND u.cpf LIKE ?";
        $like = "%$busca%";
        $params = [$like];
        $types = "s";
    } else {
        $where .= " AND (u.nome LIKE ? OR u.cpf LIKE ? OR l.login_via LIKE ? OR l.ip LIKE ?)";
        $like = "%$busca%";
        $params = [$like, $like, $like, $like];
        $types = "ssss";
    }
}

$sql = "SELECT l.*, u.nome as usuario_nome, u.cpf as usuario_cpf 
        FROM logs l 
        LEFT JOIN users u ON l.usuario_id = u.id 
        $where 
        ORDER BY l.data_hora DESC 
        LIMIT ? OFFSET ?";
        
$stmt = $conn->prepare($sql);

if(!empty($busca)){
    $params[] = $limite;
    $params[] = $offset;
    $types .= "ii";
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $limite, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$sql_total = "SELECT COUNT(*) as total FROM logs l LEFT JOIN users u ON l.usuario_id = u.id $where";
$stmt_total = $conn->prepare($sql_total);

if(!empty($busca)){
    if($filtro === 'nome'){
        $stmt_total->bind_param("s", $like);
    } elseif($filtro === 'cpf'){
        $stmt_total->bind_param("s", $like);
    } else {
        $stmt_total->bind_param("ssss", $like, $like, $like, $like);
    }
}

$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total_logs = $total_row['total'];
$total_paginas = ceil($total_logs / $limite);

function formatarDetalhe2FA($detalhe) {
    if (strpos($detalhe, 'campo:') !== false) {
        preg_match('/campo: (\w+)/', $detalhe, $matches);
        if (isset($matches[1])) {
            $campo = $matches[1];
            $perguntas = [
                'nome_materno' => 'Nome da Mãe',
                'data_nasc' => 'Data de Nascimento',
                'endereco' => 'Endereço'
            ];
            return isset($perguntas[$campo]) ? $perguntas[$campo] : $campo;
        }
    }
    return $detalhe;
}

function formatarClasseAcao($acao) {
    return str_replace('2fa', 'two_fa', $acao);
}

function formatarTextoAcao($acao) {
    $acoes = [
        'login_ok' => 'Login OK',
        'login_fail' => 'Login Falhou',
        'two_fa_ok' => '2FA OK',
        'two_fa_fail' => '2FA Falhou',
        'login_attempt' => 'Tentativa Login',
        'senha_alterada' => 'Senha Alterada'
    ];
    return $acoes[$acao] ?? $acao;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Autenticação - Raízes do Café</title>
    <link rel="stylesheet" href="../css/logs.css">
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
        <a href="../pages/masterhome.php">Home</a>
        <a href="../pages/mastermenu.php">Menu</a>
        <a href="consulta_usuarios.php">Consulta de Usuários</a>
        <a href="logs.php">Logs</a>
        <a href="modelo_bd.php">Modelo BD</a>

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
    <main class="container-logs">
        <div class="logs-header">
            <h1>Logs de Autenticação</h1>
            <p>Resumo das entradas dos usuários no sistema</p>
        </div>

        <form method="GET" class="filtro-form">
            <div class="filtro-container">
                <select name="filtro" class="filtro-select">
                    <option value="todos" <?php echo $filtro === 'todos' ? 'selected' : ''; ?>>Todos os campos</option>
                    <option value="nome" <?php echo $filtro === 'nome' ? 'selected' : ''; ?>>Nome do usuário</option>
                    <option value="cpf" <?php echo $filtro === 'cpf' ? 'selected' : ''; ?>>CPF</option>
                </select>
                
                <input 
                    type="text" 
                    name="busca" 
                    placeholder="Digite sua busca..." 
                    value="<?php echo htmlspecialchars($busca); ?>"
                    class="busca-input"
                >
                
                <button type="submit" class="btn-buscar">Buscar</button>
                <a href="logs.php" class="btn-limpar">Limpar Filtros</a>
            </div>
        </form>

        <div class="info-logs">
            <span>Total de registros: <?php echo $total_logs; ?></span>
            <span>Página <?php echo $pagina; ?> de <?php echo $total_paginas; ?></span>
        </div>

        <div class="tabela-container">
            <table class="tabela-logs">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Usuário</th>
                        <th>CPF</th>
                        <th>Login Via</th>
                        <th>Ação</th>
                        <th>2FA Utilizado</th>
                        <th>IP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($row['data_hora'])); ?></td>
                        <td><?php echo htmlspecialchars($row['usuario_nome'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['usuario_cpf'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['login_via']); ?></td>
                        <td>
                            <span class="acao <?php echo formatarClasseAcao($row['acao']); ?>">
                                <?php echo formatarTextoAcao(formatarClasseAcao($row['acao'])); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if (strpos($row['acao'], '2fa') !== false) {
                                echo formatarDetalhe2FA($row['detalhe']);
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['ip']); ?></td>
                        <td>
                            <?php if (strpos($row['acao'], 'ok') !== false): ?>
                                <span class="status sucesso">Sucesso</span>
                            <?php elseif (strpos($row['acao'], 'fail') !== false): ?>
                                <span class="status erro">Falha</span>
                            <?php else: ?>
                                <span class="status info">Info</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php if($total_paginas > 1): ?>
        <div class="paginacao">
            <?php if($pagina > 1): ?>
                <a href="?pagina=<?php echo $pagina - 1; ?>&busca=<?php echo urlencode($busca); ?>&filtro=<?php echo $filtro; ?>" class="pagina-btn">Anterior</a>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $total_paginas; $i++): ?>
                <?php if($i == $pagina): ?>
                    <span class="pagina-atual"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?pagina=<?php echo $i; ?>&busca=<?php echo urlencode($busca); ?>&filtro=<?php echo $filtro; ?>" class="pagina-btn"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if($pagina < $total_paginas): ?>
                <a href="?pagina=<?php echo $pagina + 1; ?>&busca=<?php echo urlencode($busca); ?>&filtro=<?php echo $filtro; ?>" class="pagina-btn">Próxima</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
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
    <script>
document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById("toggle-contraste");
  const logsContainer = document.querySelector("main.container-logs");

  if (!toggle || !logsContainer) return;

  // Restaura estado salvo
  const estadoSalvo = localStorage.getItem("escuroLogs");
  if (estadoSalvo === "true") {
    toggle.checked = true;
    logsContainer.classList.add("escuro-logs");
  } else {
    toggle.checked = false;
    logsContainer.classList.remove("escuro-logs");
  }

  // Evento de troca
  toggle.addEventListener("change", function () {
    const ativo = this.checked;
    logsContainer.classList.toggle("escuro-logs", ativo);
    localStorage.setItem("escuroLogs", ativo ? "true" : "false");
  });
});
</script>

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