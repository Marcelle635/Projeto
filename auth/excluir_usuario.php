<?php
session_start();
require_once("../config/db.php");

if(!isset($_SESSION['user']) || $_SESSION['user']['perfil'] !== 'master'){
    header("Location: ../pages/login.php");
    exit;
}

if(isset($_POST['id'])){
    $id = $_POST['id'];

    if($id == $_SESSION['user']['id']){
        header("Location: ../pages/consulta_usuarios.php?erro=nao_pode_excluir_se_mesmo");
        exit;
    }

    $sql = "DELETE FROM users WHERE id = ? AND perfil = 'comum'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: ../pages/consulta_usuarios.php");
exit;
?>
