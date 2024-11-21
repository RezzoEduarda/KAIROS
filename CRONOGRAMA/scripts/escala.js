const calendarGrid = document.getElementById('calendarGrid');
const monthYear = document.getElementById('monthYear');
const prevMonthBtn = document.getElementById('prevMonth');
const nextMonthBtn = document.getElementById('nextMonth');
const eventModal = document.getElementById('eventModal');
const eventDetails = document.getElementById('eventDetails');
const eventDate = document.getElementById('eventDate');
const closeModal = document.getElementById('closeModal');

let currentDate = new Date();
let userRole = 'volunteer'; // 'admin' ou 'volunteer'
let events = {
    // Estrutura de exemplo para eventos
    "2023-11-11": [
        { title: "Ensaio de Louvor", time: "10:00 AM", ministry: "Louvor", assigned: true },
        { title: "Culto da Família", time: "07:00 PM", ministry: "Mídia", assigned: false },
    ],
    "2023-11-20": [
        { title: "Ensaio de Dança", time: "06:00 PM", ministry: "Dança", assigned: true }
    ]
};

function renderCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();

    monthYear.textContent = `${date.toLocaleString('default', { month: 'long' })} ${year}`;

    // Limpa o grid de dias
    calendarGrid.innerHTML = '';

    // Calcula o primeiro e o último dia do mês
    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);

    const startDay = firstDayOfMonth.getDay();
    const daysInMonth = lastDayOfMonth.getDate();

    // Preenche os dias em branco do início
    for (let i = 0; i < startDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.classList.add('empty');
        calendarGrid.appendChild(emptyCell);
    }

    // Preenche os dias do mês
    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.textContent = day;
        dayCell.dataset.date = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        // Marca o dia atual
        if (day === new Date().getDate() &&
            month === new Date().getMonth() &&
            year === new Date().getFullYear()) {
            dayCell.classList.add('today');
        }

        dayCell.addEventListener('click', () => showEvents(dayCell.dataset.date));
        calendarGrid.appendChild(dayCell);
    }
}

// Função para exibir eventos de um dia
function showEvents(date) {
    eventDetails.innerHTML = ''; // Limpa detalhes de eventos anteriores
    eventDate.textContent = `Eventos para ${date}`;

    const dayEvents = events[date] || [];
    if (dayEvents.length === 0) {
        eventDetails.innerHTML = "<p>Nenhum evento para este dia.</p>";
    } else {
        dayEvents.forEach(event => {
            const eventElement = document.createElement('div');
            eventElement.classList.add('event-details');
            eventElement.innerHTML = `
                <h3>${event.title}</h3>
                <p>Horário: ${event.time}</p>
                <p>Ministério: ${event.ministry}</p>
                <p>${event.assigned ? 'Você está escalado' : 'Você não está escalado'}</p>
            `;
            eventDetails.appendChild(eventElement);
        });
    }

    eventModal.style.display = 'flex';
}

closeModal.addEventListener('click', () => {
    eventModal.style.display = 'none';
});

window.addEventListener('click', (e) => {
    if (e.target === eventModal) {
        eventModal.style.display = 'none';
    }
});

// Eventos de navegação entre meses
prevMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
});

nextMonthBtn.addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
});

// Inicializa o calendário com o mês atual
renderCalendar(currentDate);
