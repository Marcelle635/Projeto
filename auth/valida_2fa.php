<?php
session_start();
require __DIR__ . "/../config/db.php";

if(!isset($_SESSION['user']) || !isset($_SESSION['campo2fa'])){
    header("Location: ../pages/login.php");
    exit;
}

$campo = $_SESSION['campo2fa'];
$resposta = trim($_POST['resposta'] ?? '');
$ip = $_SERVER['REMOTE_ADDR'] ?? null;
$user = $_SESSION['user_temp'];
$uid = $user['id'] ?? null;

function grava_log($conn, $usuario_id, $login_via, $acao, $detalhe, $ip){
    $s = mysqli_prepare($conn, "INSERT INTO logs (usuario_id, login_via, acao, detalhe, ip) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($s, "issss", $usuario_id, $login_via, $acao, $detalhe, $ip);
    mysqli_stmt_execute($s);
}

$st = mysqli_prepare($conn, "SELECT {$campo} FROM users WHERE id = ?");
mysqli_stmt_bind_param($st, "i", $uid);
mysqli_stmt_execute($st);
$r = mysqli_stmt_get_result($st);
$row = mysqli_fetch_assoc($r);
$valor_db = $row[$campo] ?? null;

$tent = $_SESSION['tentativas_2fa'] ?? 0;

function normalize_text($s){
    $s = mb_strtolower(trim($s), 'UTF-8');
    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
    $s = preg_replace('/[^a-z0-9 ]/', '', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}

$ok = false;

if($campo === 'data_nasc'){
    if($resposta === $valor_db) $ok = true;
} elseif ($campo === 'nome_materno'){
    if($valor_db !== null && normalize_text($resposta) === normalize_text($valor_db)) $ok = true;
} elseif ($campo === 'endereco'){
    if($valor_db !== null){
        $resp_norm = normalize_text($resposta);
        $db_norm = normalize_text($valor_db);

        if($resp_norm === $db_norm) {
            $ok = true;
        } else {
            
            if(strlen($resp_norm) >= 6 && strpos($db_norm, $resp_norm) !== false){
                $ok = true;
            }
        }
    }
} else {
    if($resposta === $valor_db) $ok = true;
}

if($ok){
    grava_log($conn, $uid, $user['login'] ?? null, '2fa_ok', "2FA correta (campo: $campo)", $ip);

    
    $_SESSION['logado'] = true;
    $_SESSION['id'] = $uid;
    $_SESSION['nome'] = $user['nome'] ?? '';
    $_SESSION['perfil'] = $user['perfil'] ?? 'comum';

    
    unset($_SESSION['user_temp'], $_SESSION['campo2fa'], $_SESSION['tentativas_2fa']);

    header("Location: ../pages/inicio.html");
    exit;
} else {
    $tent++;
    $_SESSION['tentativas_2fa'] = $tent;

    grava_log($conn, $uid, $user['login'] ?? null, '2fa_fail', "Resposta incorreta (campo: $campo) tentativa $tent", $ip);

    if($tent >= 3){
        session_destroy();
        header("Location: ../pages/login.php?erro=3tentativas");
        exit;
    } else {
        header("Location: ../pages/2fa.php?erro=1");
        exit;
    }
}
