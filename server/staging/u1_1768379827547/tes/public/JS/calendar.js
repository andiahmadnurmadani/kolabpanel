// Pagination for upcoming events
let currentPage = 1;
const perPage = 5;
let allEvents = [];
let calendarActivities = [];
let calendarData = {};

// Initialize data from HTML
function initializeData() {
    const dataElement = document.getElementById('calendarData');
    if (dataElement) {
        try {
            allEvents = JSON.parse(dataElement.getAttribute('data-events') || '[]');
            calendarActivities = JSON.parse(dataElement.getAttribute('data-activities') || '[]');
            calendarData = {
                storeRoute: dataElement.getAttribute('data-store-route'),
                csrfToken: dataElement.getAttribute('data-csrf-token')
            };
        } catch (e) {
            console.error('Error parsing calendar data:', e);
            allEvents = [];
            calendarActivities = [];
        }
    }
}

function renderEvents(page) {
    const container = document.getElementById('eventsContainer');
    const start = (page - 1) * perPage;
    const end = start + perPage;
    const eventsToShow = allEvents.slice(start, end);
    
    if (eventsToShow.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-slate-400">
                <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                <p class="text-sm">Tidak Ada Acara</p>
            </div>
        `;
    } else {
        container.innerHTML = eventsToShow.map(event => {
            const date = new Date(event.date);
            const dateStr = date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
            const statusBadge = event.status === 'selesai' 
                ? '<span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">Done</span>'
                : '<span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">Pending</span>';
            const timeStr = event.time ? `<p class="text-xs text-slate-400 mt-2">⏰ ${event.time.substring(0, 5)}</p>` : '';
            const deskStr = event.desk ? `<p class="text-xs text-slate-500 line-clamp-2">${event.desk}</p>` : '';
            
            return `
                <div class="p-4 rounded-xl hover:bg-slate-50 cursor-pointer border border-slate-200 bg-white" onclick="openEditModalById(${event.id})">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-xs font-semibold text-slate-400 uppercase">${dateStr}</span>
                        ${statusBadge}
                    </div>
                    <h3 class="font-bold text-slate-800 text-sm mb-1">${event.title}</h3>
                    ${deskStr}
                    ${timeStr}
                </div>
            `;
        }).join('');
    }
    
    // Re-initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    updatePaginationButtons();
}

function updatePaginationButtons() {
    const totalPages = Math.ceil(allEvents.length / perPage);
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const currentPageDisplay = document.getElementById('currentPageDisplay');
    
    if (prevBtn) prevBtn.disabled = currentPage === 1;
    if (nextBtn) nextBtn.disabled = currentPage === totalPages;
    if (currentPageDisplay) currentPageDisplay.textContent = currentPage;
}

function changePage(direction) {
    const totalPages = Math.ceil(allEvents.length / perPage);
    const newPage = currentPage + direction;
    
    if (newPage >= 1 && newPage <= totalPages) {
        currentPage = newPage;
        renderEvents(currentPage);
    }
}

function initializeCalendar(activities) {
    // Use activities from parameter
    const activitiesToUse = activities && activities.length > 0 ? activities : calendarActivities;
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Set allEvents for pagination (filter upcoming events)
    if (!allEvents || allEvents.length === 0) {
        allEvents = activitiesToUse.filter(event => {
            const eventDate = new Date(event.date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return eventDate >= today;
        }).sort((a, b) => new Date(a.date) - new Date(b.date));
    }
    
    // Render events in sidebar
    if (allEvents.length > 0) {
        renderEvents(1);
    }
    
    // Initialize pagination if there are events
    if (allEvents.length > perPage) {
        updatePaginationButtons();
    }
    
    // Convert activities to FullCalendar events
    const events = activitiesToUse.map(activity => {
        // Parse date properly - handle both string and object formats
        let dateStr = activity.date;
        if (typeof dateStr === 'object' && dateStr.date) {
            dateStr = dateStr.date.split(' ')[0]; // Get YYYY-MM-DD from timestamp
        } else if (typeof dateStr === 'string' && dateStr.includes('T')) {
            dateStr = dateStr.split('T')[0]; // Get YYYY-MM-DD from ISO string
        }
        
        return {
            id: activity.id,
            title: activity.title,
            start: dateStr + (activity.time ? 'T' + activity.time : ''),
            description: activity.desk,
            status: activity.status,
            className: 'fc-event-' + activity.status,
            backgroundColor: activity.status === 'selesai' ? '#10b981' : '#3b82f6',
            borderColor: activity.status === 'selesai' ? '#059669' : '#2563eb',
            extendedProps: {
                description: activity.desk,
                status: activity.status
            }
        };
    });

    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: ''
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day'
        },
        views: {
            dayGridMonth: {
                dayMaxEvents: 2,
                moreLinkText: 'more'
            },
            timeGridWeek: {
                slotDuration: '01:00:00',
                slotLabelInterval: '01:00',
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: true,
                allDayText: 'All Day'
            }
        },
        events: events,
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            openEditModal(info.event);
        },
        dateClick: function(info) {
            openAddModal(info.dateStr);
        },
        eventMouseEnter: function(info) {
            info.el.style.transform = 'scale(1.02)';
            info.el.style.zIndex = '10';
        },
        eventMouseLeave: function(info) {
            info.el.style.transform = 'scale(1)';
            info.el.style.zIndex = '1';
        },
        editable: false,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        height: 'auto',
        aspectRatio: 2,
        firstDay: 0,
        nowIndicator: true,
        eventDisplay: 'block',
        displayEventTime: true,
        displayEventEnd: false,
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }
    });

    calendar.render();

    // Make calendar globally accessible
    window.calendar = calendar;
    
    // View switcher buttons
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.view-btn').forEach(b => {
                b.classList.remove('active', 'bg-blue-600', 'text-white');
                b.classList.add('bg-gray-100', 'text-gray-600');
            });
            this.classList.add('active', 'bg-blue-600', 'text-white');
            this.classList.remove('bg-gray-100', 'text-gray-600');
        });
    });
}

function openAddModal(date = null) {
    const modal = document.getElementById('eventModal');
    const form = document.getElementById('eventForm');
    const modalTitle = document.getElementById('modalTitle');
    const deleteBtn = document.getElementById('deleteBtn');
    
    modal.classList.remove('hidden');
    modalTitle.textContent = 'Tambah Acara Baru';
    form.action = calendarData.storeRoute || '/kalender';
    document.getElementById('methodInput').value = 'POST';
    deleteBtn.classList.add('hidden');
    form.reset();
    
    if (date) {
        document.getElementById('inputDate').value = date;
    }
    
    // Re-initialize icons
    setTimeout(() => lucide.createIcons(), 10);
}

function openEditModal(event) {
    const modal = document.getElementById('eventModal');
    const form = document.getElementById('eventForm');
    const modalTitle = document.getElementById('modalTitle');
    const deleteBtn = document.getElementById('deleteBtn');
    
    modal.classList.remove('hidden');
    modalTitle.textContent = 'Edit Acara';
    form.action = `/kalender/${event.id}`;
    document.getElementById('methodInput').value = 'PUT';
    deleteBtn.classList.remove('hidden');
    
    document.getElementById('eventId').value = event.id;
    document.getElementById('inputTitle').value = event.title;
    document.getElementById('inputDesk').value = event.extendedProps.description || '';
    
    const startDate = new Date(event.start);
    document.getElementById('inputDate').value = startDate.toISOString().split('T')[0];
    
    if (event.start.toTimeString) {
        const time = startDate.toTimeString().slice(0, 5);
        document.getElementById('inputTime').value = time;
    }
    
    document.getElementById('inputStatus').value = event.extendedProps.status || 'pending';
    
    // Re-initialize icons
    setTimeout(() => lucide.createIcons(), 10);
}

function openEditModalById(eventId) {
    const activity = calendarActivities.find(a => a.id === eventId);
    
    if (activity) {
        const modal = document.getElementById('eventModal');
        const form = document.getElementById('eventForm');
        const modalTitle = document.getElementById('modalTitle');
        const deleteBtn = document.getElementById('deleteBtn');
        
        modal.classList.remove('hidden');
        modalTitle.textContent = 'Edit Acara';
        form.action = `/kalender/${activity.id}`;
        document.getElementById('methodInput').value = 'PUT';
        deleteBtn.classList.remove('hidden');
        
        document.getElementById('eventId').value = activity.id;
        document.getElementById('inputTitle').value = activity.title;
        document.getElementById('inputDesk').value = activity.desk || '';
        
        // Parse date properly
        let dateStr = activity.date;
        if (typeof dateStr === 'object' && dateStr.date) {
            dateStr = dateStr.date.split(' ')[0];
        } else if (typeof dateStr === 'string' && dateStr.includes('T')) {
            dateStr = dateStr.split('T')[0];
        }
        
        document.getElementById('inputDate').value = dateStr;
        document.getElementById('inputTime').value = activity.time || '';
        document.getElementById('inputStatus').value = activity.status || 'pending';
        
        // Re-initialize icons
        setTimeout(() => lucide.createIcons(), 10);
    }
}

function deleteEvent() {
    const eventId = document.getElementById('eventId').value;
    if (!eventId) return;
    
    if (confirm('Yakin ingin menghapus acara ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/kalender/${eventId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = calendarData.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function closeEventModal() {
    const modal = document.getElementById('eventModal');
    modal.classList.add('hidden');
}

// Auto reload after form submission to show new events
document.addEventListener('DOMContentLoaded', function() {
    // Initialize data from HTML
    initializeData();
    
    console.log('Calendar Activities:', calendarActivities);
    console.log('All Events:', allEvents);
    
    // Initialize calendar with activities
    initializeCalendar(calendarActivities);
    
    const eventForm = document.getElementById('eventForm');
    if (eventForm) {
        eventForm.addEventListener('submit', function(e) {
            // Let the form submit normally to Laravel controller
            // Don't prevent default or reload - Laravel will handle redirect
        });
    }
    
    // Close modal on outside click
    document.getElementById('eventModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEventModal();
        }
    });
});
