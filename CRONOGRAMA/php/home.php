<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    // Redireciona para a página de login se a sessão não estiver ativa
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Kairos</title>
    <link rel="stylesheet" href="./../css/home.css">
    <script src="./../scripts/home.js"></script>
</head>
<body>
<div class="home-container">
    <header>
        <img src="./../assets/notificacao.png" alt="Notificações" class="icon notification-icon">
        <img src="./../assets/config.png" alt="Configurações" class="icon settings-icon">
    </header>

    <div class="logo-container">
        <img src="./../assets/igreja.png" alt="Igreja" class="logo-icon">
    </div>

    <div class="menu">
        <p>Bem-vindo!</p> <!-- Saudação personalizada -->

        <button class="menu-button" id="suaEscala">
            <img src="./../assets/relogio-calendario.png" alt="Ícone de calendário" class="button-icon">
            <span>sua escala</span>
        </button>

        <button class="menu-button" id="escalaGeral">
            <img src="./../assets/calendario.png" alt="Ícone de calendário" class="button-icon">
            <span>escala geral</span>
        </button>

        <button class="menu-button" id="seuPerfil">
            <img src="./../assets/do-utilizador.png" alt="Ícone de perfil" class="button-icon">
            <span>seu perfil</span>
        </button>
    </div>

    <footer>

        <a href="logout.php" class="logout-button">Sair</a>
    </footer>
</div>

<script>
    // Redireciona para as páginas corretas ao clicar nos botões
    document.getElementById('suaEscala').addEventListener('click', function() {
        window.location.href = 'escala-voluntario.php';
    });

    document.getElementById('escalaGeral').addEventListener('click', function() {
        window.location.href = 'escala-geral.php';
    });

    document.getElementById('seuPerfil').addEventListener('click', function() {
        window.location.href = 'perfil.php';
    });
</script>

</body>
</html>

