<?php
session_start();
require __DIR__ . "/../config/db.php";

$login = $_POST['login'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($login) || empty($senha)) {
    header("Location: ../pages/login.php?erro=1");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE login = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user) {
    header("Location: ../pages/login.php?erro=1");
    exit;
}

$stored = $user['senha'];

$login_ok = false;

if (strlen($stored) === 64 && hash('sha256', $senha) === $stored) {
    $login_ok = true;
} elseif (password_verify($senha, $stored)) {
    $login_ok = true;
}

if (!$login_ok) {
    header("Location: ../pages/login.php?erro=1");
    exit;
}

$_SESSION['user_temp'] = [
    'id' => $user['id'],
    'nome' => $user['nome'],
    'perfil' => $user['perfil'],
    'endereco' => $user['endereco']
];

$_SESSION['tentativas_2fa'] = 0;

header("Location: ../pages/2fa.php");
exit;
