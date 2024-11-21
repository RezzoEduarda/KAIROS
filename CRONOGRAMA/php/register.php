<?php
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

// Processar o formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $numero = trim($_POST['numero']);
    $genero = $_POST['genero'];
    $ministerio = $_POST['ministerio'];

    // Validação de campos
    if (empty($nome) || empty($email) || empty($senha) || empty($numero) || empty($genero) || empty($ministerio)) {
        die("Por favor, preencha todos os campos.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Email inválido.");
    }

    $fotoPath = null;

    // Processar upload da foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $fotoNome = basename($_FILES['foto']['name']);
        $fotoExtensao = strtolower(pathinfo($fotoNome, PATHINFO_EXTENSION));

        // Validar a extensão do arquivo
        $extensoesPermitidas = ['jpg', 'jpeg', 'png'];
        if (!in_array($fotoExtensao, $extensoesPermitidas)) {
            die("Formato de imagem inválido. Apenas JPG, JPEG e PNG são permitidos.");
        }

        // Gerar um nome único para o arquivo
        $fotoPath = "uploads/" . uniqid('foto_', true) . "." . $fotoExtensao;

        // Criar a pasta de uploads se não existir
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Mover a foto para a pasta de uploads
        if (!move_uploaded_file($fotoTmp, $fotoPath)) {
            die("Erro ao salvar a foto.");
        }
    }

    try {
        // Verificar se o email já está cadastrado
        $queryCheckEmail = "SELECT id_usuario FROM Usuarios WHERE email = :email";
        $stmtCheckEmail = $pdo->prepare($queryCheckEmail);
        $stmtCheckEmail->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtCheckEmail->execute();

        if ($stmtCheckEmail->rowCount() > 0) {
            die("Este email já está cadastrado.");
        }

        // Inserir o novo usuário na tabela Usuarios
        $queryUsuario = "INSERT INTO Usuarios (nome, email, senha, numero, genero, ministerio, tipo, nivel_acesso, foto) 
                         VALUES (:nome, :email, :senha, :numero, :genero, :ministerio, 'Voluntário', '1', :foto)";
        $stmt = $pdo->prepare($queryUsuario);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);
        $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
        $stmt->bindParam(':genero', $genero, PDO::PARAM_STR);
        $stmt->bindParam(':ministerio', $ministerio, PDO::PARAM_STR);
        $stmt->bindParam(':foto', $fotoPath, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new Exception("Erro ao cadastrar usuário: " . implode(", ", $stmt->errorInfo()));
        }

        $idUsuario = $pdo->lastInsertId();

        session_start();
        $_SESSION['id_usuario'] = $idUsuario;
        $_SESSION['nome'] = $nome;

        header("Location: home.php");
        exit();
    } catch (Exception $e) {
        die("Erro ao cadastrar: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Kairos</title>
    <link rel="stylesheet" href="./../css/cadastro.css">
</head>
<body>
<div class="register-container">
    <div class="register-box">
        <h1>Cadastro</h1>
        <form id="registerForm" action="register.php" method="POST">
            <input type="text" name="nome" id="name" placeholder="Nome" required>
            <input type="email" name="email" id="email" placeholder="Email" required>

            <select name="genero" id="genero" required>
                <option value="">Gênero</option>
                <option value="M">Masculino</option>
                <option value="F">Feminino</option>
            </select>

            <select name="ministerio" id="ministerio" required>
                <option value="">Ministérios</option>
                <option value="Mídia">Mídia</option>
                <option value="Dança">Dança</option>
                <option value="Louvor">Louvor</option>
                <option value="Kids">Kids</option>
                <option value="Atmosfera">Atmosfera</option>
            </select>

            <label for="foto">Foto do Perfil:</label>
            <input type="file" name="foto" id="foto" accept="image/*">

            <input type="text" name="numero" id="numero" placeholder="Número de telefone" required>
            <input type="password" name="senha" id="password" placeholder="Senha" required>
            <input type="password" id="confirmPassword" placeholder="Confirmar senha" required>

            <span id="passwordError" style="color: red; display: none;">As senhas não coincidem.</span>

            <button type="submit" class="register-button">Concluir</button>
        </form>
    </div>
</div>

<script>
    // Validação de senha
    const registerForm = document.getElementById('registerForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordError = document.getElementById('passwordError');

    registerForm.addEventListener('submit', function(event) {
        if (password.value !== confirmPassword.value) {
            event.preventDefault();
            passwordError.style.display = 'block';
        } else {
            passwordError.style.display = 'none';
        }
    });
</script>
</body>
</html>
