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

// Obter apenas voluntários cadastrados
$query = "SELECT id_usuario, nome FROM Usuarios WHERE tipo = 'Voluntário'";
$stmt = $pdo->query($query);
$voluntarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processa o envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_evento = trim($_POST['event-name']);
    $data_evento = $_POST['event-date'];
    $hora_inicio = $_POST['start-time'];
    $hora_fim = $_POST['end-time'];
    $id_voluntario = $_POST['volunteer']; // ID do voluntário selecionado

    // Insere os dados na tabela de eventos
    try {
        $query = "INSERT INTO eventos (nome, data, horario_inicio, horario_fim, voluntario) 
                  VALUES (:nome, :data, :hora_inicio, :hora_fim, :voluntario)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nome', $nome_evento);
        $stmt->bindParam(':data', $data_evento);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fim', $hora_fim);
        $stmt->bindParam(':voluntario', $id_voluntario); // Usar o ID do voluntário

        $stmt->execute();

        // Redireciona para a página de listagem de eventos após a criação
        echo "<script>window.location.href='listar-eventos.php';</script>";
    } catch (Exception $e) {
        echo "Erro ao criar evento: " . $e->getMessage();
    }
}


// Obter todos os eventos para exibir no calendário
$query = "SELECT * FROM eventos";
$stmt = $pdo->query($query);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Eventos</title>
    <link rel="stylesheet" href="./../css/criar-escala.css">
</head>
<body>
<div class="container">
    <header>
        <button class="back-button" onclick="window.location.href='adm-home.php'">Voltar</button>
        <h2>Ministério</h2>
    </header>
    <div class="content">
        <div class="event-form">
            <h3>Escolher Data</h3>
            <!-- Formulário para Criar Evento -->
            <form action="criar-escala.php" method="POST">
                <label for="event-name">Evento</label>
                <input type="text" id="event-name" name="event-name" placeholder="Nome do evento" required>

                <label for="event-date">Data</label>
                <input type="date" id="event-date" name="event-date" required>

                <label for="event-time">Horário</label>
                <div class="time-inputs">
                    <input type="time" id="start-time" name="start-time" required>
                    <span>-</span>
                    <input type="time" id="end-time" name="end-time" required>
                </div>

                <label for="volunteer">Voluntário</label>
                <select id="volunteer" name="volunteer" required>
                    <option value="">Selecione um voluntário</option>
                    <?php foreach ($voluntarios as $voluntario): ?>
                        <option value="<?php echo $voluntario['id_usuario']; ?>">
                            <?php echo $voluntario['nome']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="add-button">Adicionar Evento</button>
                <a href="listar-eventos.php">LISTA DE EVENTOS</a>
            </form>
        </div>

        <!-- Calendário de Eventos -->
        <div id="calendar" class="calendar">
            <?php foreach ($eventos as $evento): ?>
                <div class="calendar-event" style="grid-column: span 1; background-color: #0078d4; color: #fff;">
                    <strong><?php echo $evento['nome']; ?></strong><br>
                    <?php echo date('d/m/Y', strtotime($evento['data'])); ?><br>
                    <?php echo date('H:i', strtotime($evento['horario_inicio'])) . ' - ' . date('H:i', strtotime($evento['horario_fim'])); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script src="./../scripts/criar-escala.js"></script>
</body>
</html>
