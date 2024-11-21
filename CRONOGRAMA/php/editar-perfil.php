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

// Buscar informações do usuário logado
try {
    $query = "SELECT id_usuario, nome, email, numero, genero, ministerio, foto, tipo FROM Usuarios WHERE id_usuario = :id_usuario";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuário não encontrado.");
    }

    // Determinar a página de volta baseada no tipo de usuário
    $backLink = 'home.php';  // Link padrão para Voluntário
    if ($usuario['tipo'] == 'Administrador' || $usuario['tipo'] == 'Supervisor') {
        $backLink = 'adm-home.php';  // Redireciona para adm-home.php para administradores e supervisores
    }
} catch (Exception $e) {
    die("Erro ao carregar perfil: " . $e->getMessage());
}

// Atualizar perfil do usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $numero = trim($_POST['numero']);
    $genero = $_POST['genero'];
    $ministerio = $_POST['ministerio'];

    // Processar a imagem se houver
    $foto = $usuario['foto'];  // Manter a foto existente por padrão

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['foto']['name']);

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadFile)) {
            $foto = $uploadFile;  // Atualiza o caminho da foto
        } else {
            die("Erro ao fazer upload da foto.");
        }
    }

    try {
        // Atualiza os dados no banco
        $queryUpdate = "UPDATE Usuarios SET nome = :nome, email = :email, numero = :numero, genero = :genero, ministerio = :ministerio, foto = :foto WHERE id_usuario = :id_usuario";
        $stmtUpdate = $pdo->prepare($queryUpdate);
        $stmtUpdate->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':numero', $numero, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':genero', $genero, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':ministerio', $ministerio, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':foto', $foto, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':id_usuario', $_SESSION['id_usuario'], PDO::PARAM_INT);

        if (!$stmtUpdate->execute()) {
            throw new Exception("Erro ao atualizar perfil: " . implode(", ", $stmtUpdate->errorInfo()));
        }

        // Atualiza os dados na sessão após a mudança
        $_SESSION['nome'] = $nome;

        // Redireciona para a página inicial de acordo com o tipo de usuário
        header("Location: " . $backLink);
        exit();
    } catch (Exception $e) {
        die("Erro ao atualizar perfil: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="./../css/editar-perfil.css">
</head>
<body>
<div class="container">
    <header>
        <!-- Botão de voltar redirecionando conforme o tipo de usuário -->
        <button class="back-button" onclick="window.location.href='<?= htmlspecialchars($backLink) ?>'">Voltar</button>
        <h2>Editar Perfil</h2>
    </header>

    <div class="profile-card">
        <div class="profile-image">
            <!-- Exibe a foto do usuário ou uma imagem padrão caso não exista -->
            <img src="<?= htmlspecialchars($usuario['foto'] ?? './../assets/default-profile.png') ?>" alt="Foto do Usuário">
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="profile-info">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

                <label for="numero">Número de Telefone:</label>
                <input type="text" id="numero" name="numero" value="<?= htmlspecialchars($usuario['numero']) ?>">

                <label for="genero">Gênero:</label>
                <select name="genero" id="genero" required>
                    <option value="masculino" <?= ($usuario['genero'] === 'masculino') ? 'selected' : '' ?>>Masculino</option>
                    <option value="feminino" <?= ($usuario['genero'] === 'feminino') ? 'selected' : '' ?>>Feminino</option>
                    <option value="Outro" <?= ($usuario['genero'] === 'Outro') ? 'selected' : '' ?>>Outro</option>
                </select>

                <label for="ministerio">Ministério:</label>
                <select name="ministerio" id="ministerio" required>
                    <option value="Mídia" <?= ($usuario['ministerio'] === 'Mídia') ? 'selected' : '' ?>>Mídia</option>
                    <option value="Dança" <?= ($usuario['ministerio'] === 'Dança') ? 'selected' : '' ?>>Dança</option>
                    <option value="Louvor" <?= ($usuario['ministerio'] === 'Louvor') ? 'selected' : '' ?>>Louvor</option>
                    <option value="Kids" <?= ($usuario['ministerio'] === 'Kids') ? 'selected' : '' ?>>Kids</option>
                    <option value="Atmosfera" <?= ($usuario['ministerio'] === 'Atmosfera') ? 'selected' : '' ?>>Atmosfera</option>
                </select>

                <label for="foto">Foto de Perfil:</label>
                <input type="file" name="foto" id="foto" accept="image/*">

            </div>

            <button type="submit" class="edit-button">Salvar Alterações</button>
        </form>
    </div>
</div>
</body>
</html>
