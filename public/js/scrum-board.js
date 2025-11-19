// JS para drag & drop de tareas en el Sprint Board

document.addEventListener('DOMContentLoaded', function () {
    const columns = document.querySelectorAll('.scrum-column');
    let draggedCard = null;

    document.querySelectorAll('.scrum-card').forEach(card => {
        card.addEventListener('dragstart', function (e) {
            draggedCard = card;
            e.dataTransfer.effectAllowed = 'move';
        });
    });

    columns.forEach(column => {
        column.addEventListener('dragover', function (e) {
            e.preventDefault();
        });
        column.addEventListener('drop', function (e) {
            e.preventDefault();
            if (!draggedCard) return;
            const tareaId = draggedCard.getAttribute('data-tarea-id');
            const newFaseId = column.getAttribute('data-fase-id');
            // Mover visualmente
            column.querySelector('.scrum-tasks').appendChild(draggedCard);
            // Llamar backend para actualizar
            fetch(`/api/tareas/${tareaId}/cambiar-fase`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id_fase: newFaseId })
            }).then(res => {
                if (!res.ok) alert('Error al mover la tarea');
            });
        });
    });
});
