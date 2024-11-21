<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Configuração de conexão com o banco de dados
$host = '127.0.0.1';
$dbname = 'CronogramaApp';
$username = 'root';
$password = '!Hunt3rP1s08642';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}

try {
    // Busca informações do usuário logado, incluindo o tipo de usuário
    $query = "SELECT nome, email, numero, genero, ministerio, foto, tipo FROM Usuarios WHERE id_usuario = :id_usuario";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuário não encontrado.");
    }

    // Determinar o link de "Voltar" com base no tipo de usuário
    $backLink = '';
    if ($usuario['tipo'] === 'Voluntário') {
        $backLink = 'home.php';
    } else if ($usuario['tipo'] === 'Supervisor' || $usuario['tipo'] === 'Administrador') {
        $backLink = 'adm-home.php';
    }

} catch (Exception $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <link rel="stylesheet" href="./../css/perfil.css">
</head>
<body>
<div class="container">
    <header>
        <!-- O link do botão "Voltar" é dinâmico dependendo do tipo de usuário -->
        <button class="back-button" onclick="window.location.href='<?= htmlspecialchars($backLink) ?>'">Voltar</button>
        <h2>Perfil</h2>
    </header>

    <div class="profile-card">
        <div class="profile-image">
            <!-- Exibe a foto do usuário ou uma imagem padrão caso não exista -->
            <img src="<?= htmlspecialchars($usuario['foto'] ?? './../assets/default-profile.png') ?>"
                 alt="Foto do Usuário">
        </div>
        <div class="profile-info">
            <h3><?= htmlspecialchars($usuario['nome']) ?></h3>
            <p>Email: <?= htmlspecialchars($usuario['email']) ?></p>
            <p>Telefone: <?= htmlspecialchars($usuario['numero']) ?></p>
            <p>Gênero: <?= htmlspecialchars($usuario['genero']) ?></p>
            <p>Ministério: <?= htmlspecialchars($usuario['ministerio']) ?></p>
        </div>
        <button class="edit-button" onclick="window.location.href='editar-perfil.php'">Editar Perfil</button>

    </div>
</div>
</body>
</html>
