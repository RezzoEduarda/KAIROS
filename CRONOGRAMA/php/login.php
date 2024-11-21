<?php
// Configuração de conexão com o banco de dados
$host = '127.0.0.1';
$dbname = 'CronogramaApp';
$username = 'root';
$password = '!Hunt3rP1s08642';

session_start();

// Verifica se o usuário já está logado
if (isset($_SESSION['id_usuario'])) {
    if ($_SESSION['tipo'] === 'Administrador' || $_SESSION['tipo'] === 'Supervisor') {
        header("Location: adm-home.php");
    } else {
        header("Location: home.php");
    }
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}

// Processar o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['password']);

    if (!empty($email) && !empty($senha)) {
        // Consulta o banco de dados para verificar as credenciais
        $query = "SELECT * FROM Usuarios WHERE email = :email AND senha = :senha";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':senha', $senha, PDO::PARAM_STR); // Comparação direta da senha
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // Inicia uma sessão para o usuário
            session_start();
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['tipo'] = $usuario['tipo'];
            $_SESSION['nivel_acesso'] = $usuario['nivel_acesso'];

            // Redireciona com base no tipo ou nível de acesso
            if ($usuario['tipo'] === 'Administrador' || $usuario['tipo'] === 'Supervisor') {
                header("Location: adm-home.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $erro = "Email ou senha inválidos.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kairos</title>
    <link rel="stylesheet" href="./../css/login.css">
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <h1 class="logo">KAIROS</h1>
        <h2>Login</h2>
        <?php if (!empty($erro)): ?>
            <p class="error-message"><?php echo $erro; ?></p>
        <?php endif; ?>
        <form id="loginForm" action="login.php" method="POST">
            <input type="text" name="email" id="email" placeholder="Email" required>
            <input type="password" name="password" id="password" placeholder="Senha" required>
            <button type="submit" class="login-button">Login</button>
        </form>
        <p class="signup-text">Não tem conta? <a href="register.php">Crie sua conta!</a></p>
    </div>
</div>
</body>
</html>
