<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

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

// Obter o ID do usuário logado
$id_usuario = $_SESSION['id_usuario'];

// Obter os eventos do voluntário para o mês atual
$mes_atual = date('m');
$ano_atual = date('Y');
$query = "
    SELECT e.*, u.nome AS voluntario_nome 
    FROM eventos e 
    JOIN Usuarios u ON e.voluntario = u.id_usuario 
    WHERE e.voluntario = :id_usuario AND MONTH(e.data) = :mes AND YEAR(e.data) = :ano
";
$stmt = $pdo->prepare($query);
$stmt->execute(['id_usuario' => $id_usuario, 'mes' => $mes_atual, 'ano' => $ano_atual]);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escala - Voluntário</title>
    <link rel="stylesheet" href="./../css/escala.css">
</head>
<body>

<div class="calendar-container">
    <header class="calendar-header">
        <button class="nav-button" id="prevMonth">❮</button>
        <h1 id="monthYear"><?php echo date('F Y'); ?></h1>
        <button class="nav-button" id="nextMonth">❯</button>
    </header>

    <div class="calendar-days">
        <div>Dom</div>
        <div>Seg</div>
        <div>Ter</div>
        <div>Qua</div>
        <div>Qui</div>
        <div>Sex</div>
        <div>Sáb</div>
    </div>

    <div class="calendar-grid" id="calendarGrid"></div>
</div>

<!-- Modal para Visualização de Eventos -->
<div class="modal" id="eventModal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2 id="eventDate">Eventos do dia</h2>
        <div id="eventDetails"></div>
    </div>
</div>

<footer>
    <button class="back-button" onclick="window.history.back()">❮ Voltar</button>
</footer>

<script src="./../scripts/escala.js"></script>
<script>
    // Variables for current month and year
    let currentMonth = <?php echo $mes_atual; ?>;
    let currentYear = <?php echo $ano_atual; ?>;

    // Recebe os eventos do PHP e os formata para JavaScript
    const eventos = <?php echo json_encode($eventos); ?>;

    // Function to generate the calendar for the given month and year
    function generateCalendar(month, year) {
        const firstDay = new Date(year, month - 1, 1);
        const lastDay = new Date(year, month, 0);
        const daysInMonth = lastDay.getDate();
        const startDay = firstDay.getDay();

        let calendarGrid = document.getElementById('calendarGrid');
        calendarGrid.innerHTML = '';

        // Fill empty cells before the first day of the month
        for (let i = 0; i < startDay; i++) {
            const emptyCell = document.createElement('div');
            calendarGrid.appendChild(emptyCell);
        }

        // Create cells for each day of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement('div');
            dayCell.innerText = day;

            // Highlight today's date
            const today = new Date();
            if (today.getDate() === day && today.getMonth() === month - 1 && today.getFullYear() === year) {
                dayCell.classList.add('today');
            }

            // Verificar se o dia tem evento
            eventos.forEach(evento => {
                const eventoDate = new Date(evento.data);
                if (eventoDate.getDate() === day && eventoDate.getMonth() === month - 1 && eventoDate.getFullYear() === year) {
                    dayCell.classList.add('event');
                    dayCell.setAttribute('data-event', JSON.stringify(evento));
                }
            });

            dayCell.addEventListener('click', function() {
                if (dayCell.classList.contains('event')) {
                    const evento = JSON.parse(dayCell.getAttribute('data-event'));
                    document.getElementById('eventDate').innerText = `Evento: ${evento.nome}`;
                    document.getElementById('eventDetails').innerHTML = `
            <p>Data: ${evento.data}</p>
            <p>Descrição: ${evento.descricao}</p>
            <p>Horário de Início: ${evento.horario_inicio}</p>
            <p>Horário de Fim: ${evento.horario_fim}</p>
        `;
                    document.getElementById('eventModal').style.display = 'flex';
                }
            });


            calendarGrid.appendChild(dayCell);
        }
    }

    // Navigate to previous month
    document.getElementById('prevMonth').addEventListener('click', function() {
        if (currentMonth === 1) {
            currentMonth = 12;
            currentYear--;
        } else {
            currentMonth--;
        }
        generateCalendar(currentMonth, currentYear);
        document.getElementById('monthYear').innerText = `${new Date(currentYear, currentMonth - 1).toLocaleString('default', { month: 'long' })} ${currentYear}`;
    });

    // Navigate to next month
    document.getElementById('nextMonth').addEventListener('click', function() {
        if (currentMonth === 12) {
            currentMonth = 1;
            currentYear++;
        } else {
            currentMonth++;
        }
        generateCalendar(currentMonth, currentYear);
        document.getElementById('monthYear').innerText = `${new Date(currentYear, currentMonth - 1).toLocaleString('default', { month: 'long' })} ${currentYear}`;
    });

    // Close modal
    document.getElementById('closeModal').addEventListener('click', function() {
        document.getElementById('eventModal').style.display = 'none';
    });

    // Initial calendar generation
    generateCalendar(currentMonth, currentYear);
</script>

</body>
</html>
