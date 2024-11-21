<?php
// Conexão com o banco de dados
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

// Processa a exclusão de um evento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['evento_id'])) {
    $evento_id = $_POST['evento_id'];

    try {
        // Excluindo o evento pelo ID
        $query = "DELETE FROM eventos WHERE id = :evento_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':evento_id', $evento_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirecionar após excluir
        header('Location: listar-eventos.php');
        exit;
    } catch (PDOException $e) {
        echo "Erro ao excluir evento: " . $e->getMessage();
    }
}

// Obter todos os eventos
$query = "SELECT e.*, u.nome AS voluntario_nome FROM eventos e JOIN Usuarios u ON e.voluntario = u.id_usuario";
$stmt = $pdo->query($query);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Eventos</title>
    <link rel="stylesheet" href="./../css/listar-eventos.css">
</head>
<body>
<div class="container">
    <header>
        <button class="back-button" onclick="window.location.href='adm-home.php'">Voltar</button>
        <h2>Eventos Cadastrados</h2>
    </header>

    <div class="content">
        <div class="event-list">
            <h3>Eventos Cadastrados</h3>
            <?php if (count($eventos) > 0): ?>
                <ul>
                    <?php foreach ($eventos as $evento): ?>
                        <li class="event-item">
                            <div class="event-details">
                                <h4><?php echo $evento['nome']; ?></h4>
                                <p><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($evento['data'])); ?></p>
                                <p><strong>Horário:</strong> <?php echo date('H:i', strtotime($evento['horario_inicio'])) . ' - ' . date('H:i', strtotime($evento['horario_fim'])); ?></p>
                                <p><strong>Voluntário:</strong> <?php echo $evento['voluntario_nome']; ?></p>
                                <p><strong>Ministério:</strong> <?php echo $evento['ministerio']; ?></p>
                            </div>
                            <a href="escala-geral.php?ministerio=<?php echo urlencode($evento['ministerio']); ?>" class="view-button">Ver Escala Geral</a>
                            <form action="listar-eventos.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este evento?');">
                                <input type="hidden" name="evento_id" value="<?php echo $evento['id']; ?>"> <!-- ID do evento -->
                                <button type="submit" class="delete-button">Excluir</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>


            <?php else: ?>
                <p>Nenhum evento cadastrado.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="./../scripts/criar-escala.js"></script>
</body>
</html>
