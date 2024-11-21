document.addEventListener('DOMContentLoaded', () => {
    const calendar = document.getElementById('calendar');
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();

    function renderCalendar(month, year) {
        calendar.innerHTML = '';

        // Cabeçalho dos dias da semana
        const daysHeader = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        daysHeader.forEach(day => {
            const headerCell = document.createElement('div');
            headerCell.classList.add('header');
            headerCell.textContent = day;
            calendar.appendChild(headerCell);
        });

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            calendar.appendChild(emptyCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = document.createElement('div');
            dayCell.textContent = day;
            dayCell.addEventListener('click', () => {
                alert(`Dia ${day} de ${month + 1}/${year} selecionado.`);
                // Implementar a lógica para exibir eventos salvos
            });
            calendar.appendChild(dayCell);
        }
    }

    renderCalendar(currentMonth, currentYear);

    document.getElementById('add-event').addEventListener('click', () => {
        const eventName = document.getElementById('event-name').value;
        const eventDate = document.getElementById('event-date').value;
        const startTime = document.getElementById('start-time').value;
        const endTime = document.getElementById('end-time').value;
        const volunteer = document.getElementById('volunteer').value;

        if (eventName && eventDate && startTime && endTime && volunteer) {
            alert(`Evento "${eventName}" adicionado para a data ${eventDate} das ${startTime} às ${endTime}.\nVoluntário: ${volunteer}`);
            // Implementar lógica para salvar o evento em um banco de dados SQL usando AJAX ou uma solicitação HTTP.
        } else {
            alert('Por favor, preencha todos os campos!');
        }
    });
});
