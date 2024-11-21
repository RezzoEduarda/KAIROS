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

// Obter o ministério a partir da URL
$ministerio = isset($_GET['ministerio']) ? $_GET['ministerio'] : '';

// Consultar eventos apenas do ministério selecionado
$query = "SELECT e.*, u.nome AS voluntario_nome FROM eventos e JOIN Usuarios u ON e.voluntario = u.id_usuario WHERE e.ministerio = :ministerio";
$stmt = $pdo->prepare($query);
$stmt->execute(['ministerio' => $ministerio]);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escala Geral</title>
    <link rel="stylesheet" href="./../css/escala.css">
</head>
<body>

<div class="calendar-container">
    <header class="calendar-header">
        <button class="nav-button" id="prevMonth">❮</button>
        <h1 id="monthYear"></h1>
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

<!-- Código JavaScript -->
<script>
    // Array com os nomes dos meses
    const monthNames = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];

    // Receber eventos do PHP
    const eventos = <?php echo json_encode($eventos); ?>;

    // Função para gerar o calendário
    function generateCalendar(month, year) {
        const firstDay = new Date(year, month - 1, 1);
        const lastDay = new Date(year, month, 0);
        const daysInMonth = lastDay.getDate();
        const startDay = firstDay.getDay();

        // Atualizar o título com o nome do mês e ano
        const monthYearElement = document.getElementById('monthYear');
        monthYearElement.innerText = `${monthNames[month - 1]} ${year}`;

        let calendarGrid = document.getElementById('calendarGrid');
        calendarGrid.innerHTML = '';

        // Preencher dias vazios antes do início do mês
        for (let i = 0; i < startDay; i++) {
            const emptyCell = document.createElement('div');
            calendarGrid.appendChild(emptyCell);
        }

        // Preencher dias com eventos
        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement('div');
            dayCell.innerText = day;

            // Destacar eventos
            eventos.forEach(evento => {
                const eventoDate = new Date(evento.data);
                if (eventoDate.getDate() === day && eventoDate.getMonth() === month - 1 && eventoDate.getFullYear() === year) {
                    dayCell.classList.add('event');
                    dayCell.setAttribute('data-event', JSON.stringify(evento));
                }
            });

            // Adicionar evento de clique
            dayCell.addEventListener('click', function () {
                if (dayCell.classList.contains('event')) {
                    const evento = JSON.parse(dayCell.getAttribute('data-event'));
                    document.getElementById('eventDate').innerText = 'Evento: ' + evento.nome;
                    document.getElementById('eventDetails').innerHTML = `
                        <p><strong>Data:</strong> ${evento.data}</p>
                        <p><strong>Horário:</strong> ${evento.horario_inicio} - ${evento.horario_fim}</p>
                        <p><strong>Voluntário:</strong> ${evento.voluntario_nome}</p>
                        <p><strong>Descrição:</strong> ${evento.descricao || 'Sem descrição'}</p>
                    `;
                    document.getElementById('eventModal').style.display = 'flex';
                }
            });

            calendarGrid.appendChild(dayCell);
        }
    }

    // Inicializar calendário com mês atual
    const today = new Date();
    let currentMonth = today.getMonth() + 1;
    let currentYear = today.getFullYear();
    generateCalendar(currentMonth, currentYear);

    // Navegação entre meses
    document.getElementById('prevMonth').addEventListener('click', function () {
        currentMonth--;
        if (currentMonth < 1) {
            currentMonth = 12;
            currentYear--;
        }
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('nextMonth').addEventListener('click', function () {
        currentMonth++;
        if (currentMonth > 12) {
            currentMonth = 1;
            currentYear++;
        }
        generateCalendar(currentMonth, currentYear);
    });

    // Fechar modal
    document.getElementById('closeModal').addEventListener('click', function () {
        document.getElementById('eventModal').style.display = 'none';
    });
</script>


</body>
</html>
