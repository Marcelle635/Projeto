<?php
session_start();
require_once("../config/db.php");

$mensagem = "";
$erro = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $data_nasc = $_POST['data_nascimento'] ?? '';
    $nome_materno = $_POST['nome_materno'] ?? '';
    $sexo = $_POST['sexo'] ?? '';
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirme_senha = $_POST['confirme_senha'] ?? '';
    $cep = $_POST['cep'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $complemento = $_POST['complemento'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';

    $erros_validacao = [];

    if(strlen($nome) < 15 || strlen($nome) > 80) {
        $erros_validacao[] = "O nome deve ter entre 15 e 80 caracteres.";
    }
    if(!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nome)) {
        $erros_validacao[] = "O nome deve conter apenas letras.";
    }

    if(!validarCPF($cpf)) {
        $erros_validacao[] = "CPF inválido.";
    }

    if(!preg_match('/^\(\+\d{1,3}\)\d{2}-\d{8,9}$/', $telefone)) {
        $erros_validacao[] = "Telefone deve seguir o formato: (+XX)XX-XXXXXXXX";
    }

    if(strlen($login) !== 6 || !preg_match('/^[a-zA-Z]+$/', $login)) {
        $erros_validacao[] = "O login deve ter exatamente 6 caracteres alfabéticos.";
    }

    if(strlen($senha) !== 8 || !preg_match('/^[a-zA-Z]+$/', $senha)) {
        $erros_validacao[] = "A senha deve ter exatamente 8 caracteres alfabéticos.";
    }

    if($senha !== $confirme_senha){
        $erros_validacao[] = "As senhas não coincidem.";
    }

    $sql_check = "SELECT id FROM users WHERE login = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $login);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if($stmt_check->num_rows > 0) {
        $erros_validacao[] = "Este login já está em uso. Escolha outro.";
    }
    $stmt_check->close();

    if(empty($erros_validacao)) {
        $senha_hash = hash('sha256', $senha);

        $endereco_completo = $endereco . ", " . $numero . " - " . $bairro . ", " . $cidade . " - " . $estado;
        if(!empty($complemento)) {
            $endereco_completo .= " - " . $complemento;
        }
        
        $sql = "INSERT INTO users (nome, email, cpf, telefone, data_nasc, nome_materno, sexo, endereco, login, senha, perfil) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'comum')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $nome, $email, $cpf, $telefone, $data_nasc, $nome_materno, $sexo, $endereco_completo, $login, $senha_hash);
        
        if($stmt->execute()){
            header("Location: login.php?cadastro=sucesso");
            exit();
        } else {
            $erro = "Erro ao cadastrar usuário. Tente novamente.";
        }
    } else {
        $erro = implode("<br>", $erros_validacao);
    }
}

function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) != 11) {
        return false;
    }

    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    
    return true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raízes do café</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <style>
        .mensagem {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .campo-erro {
            border: 2px solid #dc3545 !important;
        }
        .endereco-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .full-width {
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    <header class="header">
        <section>
            <div class="left-side">
                <a href="#" class="logo">
                    <img src="../img/logo.png" alt="logo">
                </a>
                <nav class="navbar">
                    <a href="inicio.html">Home</a>
                    <a href="menuincio.html">Menu</a>
                </nav>
            </div>

            <div class="areas">
                <a href="login.php">Login</a> |
                <button id="aumentar-fonte">A+</button>
                <button id="diminuir-fonte">A-</button>
            </div>
        </section>
    </header>

    <main>
        <h1>CADASTRE-SE</h1>

        <?php if($erro): ?>
            <div class="mensagem erro"><?= $erro; ?></div>
        <?php endif; ?>

        <form id="form" method="POST" action="cadastro.php">
            <input type="text" name="nome" placeholder="Nome completo (mín. 15, máx. 80 caracteres)" required 
                   minlength="15" maxlength="80" 
                   oninput="this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g,'')">

            <input type="date" name="data_nascimento" required>

            <input type="text" name="cpf" placeholder="CPF (apenas números)" required 
                   maxlength="14" oninput="formatarCPF(this)">

            <input type="text" name="nome_materno" placeholder="Nome materno" required>

            <input type="text" name="telefone" placeholder="Telefone: (+55)XX-XXXXXXXX" required 
                   maxlength="15" oninput="formatarTelefone(this)">

            <input type="text" name="login" placeholder="Login (6 caracteres)" required 
                   maxlength="6" oninput="this.value = this.value.replace(/[^a-zA-Z]/g,'').toUpperCase()">

            <select name="sexo" required>
                <option value="">Selecione o sexo</option>
                <option value="Feminino">Feminino</option>
                <option value="Masculino">Masculino</option>
                <option value="Prefiro não dizer">Prefiro não dizer</option>
            </select>

            <input type="password" name="senha" placeholder="Senha (8 caracteres)" required 
                   maxlength="8" oninput="this.value = this.value.replace(/[^a-zA-Z]/g,'')">

            <input type="email" name="email" placeholder="E-mail" required>

            <input type="password" name="confirme_senha" placeholder="Confirme a senha" required 
                   maxlength="8" oninput="this.value = this.value.replace(/[^a-zA-Z]/g,'')">

            <div class="endereco-fields">
                <input type="text" name="cep" placeholder="CEP" required 
                       maxlength="9" oninput="formatarCEP(this)" onblur="buscarCEP(this.value)">
                <input type="text" name="numero" placeholder="Número" required>
                <input type="text" name="endereco" placeholder="Endereço" required class="full-width">
                <input type="text" name="complemento" placeholder="Complemento (opcional)">
                <input type="text" name="bairro" placeholder="Bairro" required>
                <input type="text" name="cidade" placeholder="Cidade" required>
                <input type="text" name="estado" placeholder="Estado" required maxlength="2">
            </div>

            <div class="botao">
                <button type="submit">ENVIAR</button>
                <button type="button" id="btn-limpar">LIMPAR TELA</button>
            </div>
        </form>
    </main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const html = document.documentElement;
    let tamanho = parseInt(localStorage.getItem("tamanhoFonte")) || 70;
    html.style.fontSize = tamanho + "%";

    document.getElementById("aumentar-fonte").onclick = () => {
        if (tamanho < 90) { 
            tamanho += 10; 
            html.style.fontSize = tamanho + "%"; 
            localStorage.setItem("tamanhoFonte", tamanho); 
        }
    };
    
    document.getElementById("diminuir-fonte").onclick = () => {
        if (tamanho > 50) { 
            tamanho -= 10; 
            html.style.fontSize = tamanho + "%"; 
            localStorage.setItem("tamanhoFonte", tamanho); 
        }
    };
    
    document.getElementById("btn-limpar").onclick = () => document.getElementById("form").reset();
});

function formatarCPF(campo) {
    let cpf = campo.value.replace(/\D/g, '');
    if (cpf.length > 11) cpf = cpf.substring(0, 11);
    
    if (cpf.length > 9) {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    } else if (cpf.length > 6) {
        cpf = cpf.replace(/(\d{3})(\d{3})(\d{3})/, "$1.$2.$3");
    } else if (cpf.length > 3) {
        cpf = cpf.replace(/(\d{3})(\d{3})/, "$1.$2");
    }
    campo.value = cpf;
}

function formatarTelefone(campo) {
    let telefone = campo.value.replace(/\D/g, '');
    if (telefone.length > 11) telefone = telefone.substring(0, 11);
    
    if (telefone.length > 2) {
        telefone = telefone.replace(/(\d{2})(\d{0,9})/, "(+$1)$2");
        if (telefone.length > 7) {
            telefone = telefone.replace(/(\(\+\d{2}\))(\d{5})/, "$1$2-");
        } else if (telefone.length > 6) {
            telefone = telefone.replace(/(\(\+\d{2}\))(\d{4})/, "$1$2-");
        }
    }
    campo.value = telefone;
}

function formatarCEP(campo) {
    let cep = campo.value.replace(/\D/g, '');
    if (cep.length > 8) cep = cep.substring(0, 8);
    
    if (cep.length > 5) {
        cep = cep.replace(/(\d{5})(\d{3})/, "$1-$2");
    }
    campo.value = cep;
}

function buscarCEP(cep) {
    cep = cep.replace(/\D/g, '');
    
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.querySelector('input[name="endereco"]').value = data.logradouro || '';
                    document.querySelector('input[name="bairro"]').value = data.bairro || '';
                    document.querySelector('input[name="cidade"]').value = data.localidade || '';
                    document.querySelector('input[name="estado"]').value = data.uf || '';
                    document.querySelector('input[name="numero"]').focus();
                }
            })
            .catch(error => {
                console.log('Erro ao buscar CEP. Preencha manualmente.');
            });
    }
}

document.querySelector('input[name="login"]').addEventListener('input', function(e) {
    if (this.value.length === 6) {
        this.style.borderColor = '#28a745';
    } else {
        this.style.borderColor = '#dc3545';
    }
});

document.querySelector('input[name="senha"]').addEventListener('input', function(e) {
    if (this.value.length === 8) {
        this.style.borderColor = '#28a745';
    } else {
        this.style.borderColor = '#dc3545';
    }
});
</script>
</body>

</html>
