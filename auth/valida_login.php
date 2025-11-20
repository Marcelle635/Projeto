<?php
session_start();
require __DIR__ . "/../config/db.php";

$login = $_POST['login'] ?? '';
$senha = $_POST['senha'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? null;

if(empty($login) || empty($senha)){
    header("Location: ../pages/login.php?erro=1");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, nome, login, senha, perfil, nome_materno, data_nasc, endereco FROM users WHERE login = ?");
mysqli_stmt_bind_param($stmt, "s", $login);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);

function grava_log($conn, $usuario_id, $login_via, $acao, $detalhe, $ip){
    $s = mysqli_prepare($conn, "INSERT INTO logs (usuario_id, login_via, acao, detalhe, ip) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($s, "issss", $usuario_id, $login_via, $acao, $detalhe, $ip);
    mysqli_stmt_execute($s);
}

if(!$user){
    grava_log($conn, null, $login, 'login_attempt', 'usuario nao encontrado', $ip);
    header("Location: ../pages/login.php?erro=1");
    exit;
}

$stored = $user['senha'];
$login_ok = false;

if(strlen($stored) === 64 && hash('sha256', $senha) === $stored){
    $login_ok = true;
} elseif (password_verify($senha, $stored)) {
    $login_ok = true;
}

if(!$login_ok){
    grava_log($conn, $user['id'], $login, 'login_fail', 'senha incorreta', $ip);
    header("Location: ../pages/login.php?erro=1");
    exit;
}

if($user['perfil'] === 'comum') {
    $_SESSION['user_temp'] = [
        'id' => $user['id'],
        'nome' => $user['nome'],
        'login' => $user['login'],
        'perfil' => $user['perfil'],
        'nome_materno' => $user['nome_materno'],
        'data_nasc' => $user['data_nasc'],
        'endereco' => $user['endereco']
    ];
    
    $_SESSION['tentativas_2fa'] = 0;
    
    $perguntas = ['nome_materno', 'data_nasc', 'endereco'];
    $_SESSION['campo2fa'] = $perguntas[array_rand($perguntas)];
    
    grava_log($conn, $user['id'], $login, 'login_ok', 'senha validada, redirecionando para 2FA', $ip);
    header("Location: ../pages/2fa.php");
    exit;
} else {

    $_SESSION['user'] = [
        'id' => $user['id'],
        'nome' => $user['nome'],
        'login' => $user['login'],
        'perfil' => $user['perfil']
    ];
    
    grava_log($conn, $user['id'], $login, 'login_ok', 'login direto (perfil master)', $ip);
    header("Location: ../pages/consulta_usuarios.php");
    exit;
}
?>