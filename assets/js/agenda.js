// ==========================================
// LÓGICA DEL CALENDARIO ESTILO CALENDLY
// ==========================================
const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
// Horas de ejemplo (puedes añadir o quitar)
const availableHours = ["09:00", "10:00", "11:00", "12:00", "13:00", "16:00", "17:00", "18:00"]; 

let currentDate = new Date();
let selectedDateStr = "";
let selectedTimeStr = "";

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    document.getElementById('current-month-display').innerText = `${monthNames[month]} ${year}`;

    // Calcular días
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    // Ajustar para que la semana empiece en Lunes (0 = Domingo en JS)
    const startDay = firstDay === 0 ? 6 : firstDay - 1;

    let html = '';
    const today = new Date();
    today.setHours(0,0,0,0); // Limpiar horas para comparar solo fechas

    // Celdas vacías antes del primer día del mes
    for (let i = 0; i < startDay; i++) {
        html += `<div></div>`;
    }

    // Días del mes
    for (let d = 1; d <= daysInMonth; d++) {
        const currentDay = new Date(year, month, d);
        // Formatear a YYYY-MM-DD
        const dateStr = `${year}-${String(month+1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        
        // Deshabilitar días pasados o fines de semana (opcional)
        const isPast = currentDay < today;
        const isWeekend = currentDay.getDay() === 0 || currentDay.getDay() === 6; 

        let classes = 'cal-day';
        if (isPast || isWeekend) classes += ' disabled';
        if (dateStr === selectedDateStr) classes += ' selected';

        if (isPast || isWeekend) {
            html += `<div class="${classes}">${d}</div>`;
        } else {
            html += `<div class="${classes}" onclick="selectDate('${dateStr}', this)">${d}</div>`;
        }
    }
    document.getElementById('calendar-days').innerHTML = html;
}

function changeMonth(offset) {
    currentDate.setMonth(currentDate.getMonth() + offset);
    renderCalendar();
}

function selectDate(dateStr, element) {
    selectedDateStr = dateStr;
    selectedTimeStr = ""; // Resetear hora al cambiar de día
    
    // Re-renderizar calendario para aplicar la clase .selected
    renderCalendar();

    // Mostrar columna de horas
    const timeColumn = document.getElementById('time-column');
    timeColumn.style.display = 'block';
    
    // Formatear fecha para el título
    const dateObj = new Date(dateStr);
    const displayDate = `${dateObj.getDate()} de ${monthNames[dateObj.getMonth()]}`;
    document.getElementById('selected-date-title').innerText = displayDate;

    // Ocultar botón de confirmación hasta elegir hora
    document.getElementById('confirm-booking-btn').style.display = 'none';

    renderTimeSlots();
}

function renderTimeSlots() {
    let html = '';
    const now = new Date();
    
    // Verificamos si hay citas reservadas en la base de datos para el día seleccionado
    const bookedForDay = (typeof bookedSlotsDB !== 'undefined' && bookedSlotsDB[selectedDateStr]) 
                          ? bookedSlotsDB[selectedDateStr] 
                          : [];

    availableHours.forEach(time => {
        // Extraemos el año, mes y día (cuidado que en JS los meses van de 0 a 11)
        const [year, month, day] = selectedDateStr.split('-');
        const [hour, min] = time.split(':');
        
        // Creamos un objeto de tiempo exacto para este botón
        const slotDateTime = new Date(year, month - 1, day, hour, min, 0);
        
        // Calculamos la diferencia en horas entre ahora mismo y la cita
        const diffMs = slotDateTime - now;
        const diffHours = diffMs / (1000 * 60 * 60);

        // CONDICIONES DE BLOQUEO
        const isTooSoon = diffHours < 4; // Bloquea si faltan menos de 4 horas o es pasado
        const isBooked = bookedForDay.includes(time); // Bloquea si ya está reservada en la BBDD

        let classes = 'time-btn';
        
        if (isTooSoon || isBooked) {
            // Si está bloqueada, añadimos las clases visuales de error/tachado y desactivamos el click
            classes += isBooked ? ' booked' : ' disabled';
            html += `<button type="button" class="${classes}" disabled>${time}</button>`;
        } else {
            // Si está libre, funciona con normalidad
            if (time === selectedTimeStr) classes += ' selected';
            html += `<button type="button" class="${classes}" onclick="selectTime('${time}')">${time}</button>`;
        }
    });
    
    document.getElementById('time-slots').innerHTML = html;
}

function selectTime(timeStr) {
    selectedTimeStr = timeStr;
    renderTimeSlots(); // Re-renderizar para marcar la hora seleccionada
    
    // Mostrar el botón de confirmar reserva
    const btn = document.getElementById('confirm-booking-btn');
    btn.style.display = 'flex';
    // Pequeña animación
    btn.style.animation = 'modalSlideIn 0.3s ease-out';
}

function submitBooking() {
    if (!selectedDateStr || !selectedTimeStr) {
        Swal.fire({ title: 'Error', text: 'Selecciona una fecha y hora.', icon: 'error', background: '#14141d', color: '#fff' });
        return;
    }
    
    // Pasar los valores al formulario oculto
    document.getElementById('hidden-date').value = selectedDateStr;
    document.getElementById('hidden-time').value = selectedTimeStr;
    
    // Enviar formulario real al archivo PHP
    document.getElementById('real-booking-form').submit();
}

// Inicializar el calendario al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    renderCalendar();
});

// ==========================================
// LÓGICA DE CANCELACIÓN (Añadir a agenda.js)
// ==========================================
function confirmCancel(id) {
    Swal.fire({
        title: 'Cancelar Cita',
        text: "¿Por qué deseas cancelar esta sesión?",
        input: 'text',
        inputPlaceholder: 'Ej: Me ha surgido un imprevisto...',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#FF6B6B',
        cancelButtonColor: '#333',
        confirmButtonText: 'Confirmar Cancelación',
        cancelButtonText: 'Volver',
        background: '#14141d',
        color: '#fff',
        inputValidator: (value) => {
            if (!value) {
                return 'Por favor, escribe un motivo breve.'
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Codificamos el motivo para enviarlo de forma segura por la URL
            const reason = encodeURIComponent(result.value);
            window.location.href = `appointment_cancel.php?id=${id}&reason=${reason}`;
        }
    });
}
