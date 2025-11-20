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
<body class="dark-mode">
    <header class="header">
        <a href="#" class="logo">
            <img src="../img/logo.png" alt="logo">
        </a>
        
        <nav class="navbar">
            <a href="../pages/masterhome.php">Home</a>
            <a href="../pages/mastermenu.php">Menu</a>
            <a href="logs.php">Logs</a>
            <a href="../auth/logout.php">Sair</a>
        </nav>
    </header>

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