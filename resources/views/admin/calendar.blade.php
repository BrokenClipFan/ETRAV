<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8f9fa; overflow: hidden; }
        .fc-toolbar-title { font-weight: 600 !important; font-size: 1.25rem !important; }
        .fc-event { cursor: pointer; border: none; padding: 2px 4px; border-radius: 4px; font-weight: 500; font-size: 0.8rem; }
        .fc-event-main { color: white; }
        .fc-day-today { background-color: rgba(13, 110, 253, 0.05) !important; box-shadow: inset 0 0 0 2px #0d6efd !important; }
        .fc-day-today .fc-daygrid-day-top { flex-direction: row; justify-content: center; padding-top: 5px; }
        .fc-day-today .fc-daygrid-day-number { background-color: #0d6efd; color: white !important; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-bottom: 5px; box-shadow: 0 2px 4px rgba(13, 110, 253, 0.4); }
        
        /* Layout Adjustments to fit screen */
        #main-wrapper { height: calc(100vh - 56px); display: flex; flex-direction: column; }
        #content-row { flex: 1; overflow: hidden; }
        #sidebar-col { height: 100%; background: white; border-right: 1px solid #e9ecef; display: flex; flex-direction: column; }
        .sidebar-scrollable-list { overflow-y: auto; flex: 1; padding-right: 5px; }
        #calendar-col { height: 100%; overflow: hidden; padding: 15px; }
        #calendar-container { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px; height: 100%; display: flex; flex-direction: column; }
        #calendar { flex: 1; min-height: 0; }
        
        .today-card { transition: transform 0.2s; border: 1px solid #e9ecef; }
        .today-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important; }
    </style>
</head>
<body>
    @include('admin.layouts.nav')

    <div id="main-wrapper">
        <div class="row g-0" id="content-row">
            
            <!-- Left Sidebar: Today's Information -->
            <div class="col-lg-3 col-md-4 p-4 shadow-sm" id="sidebar-col">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-calendar2-day fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Today's Schedule</h5>
                        <p class="text-muted small mb-0">{{ date('l, F j') }}</p>
                    </div>
                </div>

                <div class="d-flex gap-2 text-muted small flex-wrap mb-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary"><i class="bi bi-circle-fill me-1 small"></i> Paid 25%</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="bi bi-circle-fill me-1 small"></i> Completed</span>
                </div>
                <div class="mb-4 flex-grow-1 d-flex flex-column" style="min-height: 0;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Upcoming Dispatches</h6>
                        <span class="badge bg-primary rounded-pill">{{ $todayBookings->count() }}</span>
                    </div>
                    
                    <div class="sidebar-scrollable-list">
                    @forelse($todayBookings as $b)
                        <div class="card shadow-sm today-card mb-3 border-start border-4 {{ $b->status == 'completed' ? 'border-success' : 'border-primary' }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge {{ $b->status == 'completed' ? 'bg-success' : 'bg-primary' }} mb-2">
                                        {{ date('h:i A', strtotime($b->pickup_datetime)) }}
                                    </span>
                                    <span class="text-muted small">#{{ $b->id }}</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 text-truncate">{{ $b->package ? $b->package->name : 'Custom Package' }}</h6>
                                <p class="text-muted small mb-2"><i class="bi bi-person-fill me-1"></i> {{ $b->user->name }} ({{ $b->pax }} Pax)</p>
                                
                                <div class="d-flex align-items-center bg-light rounded p-2 mt-2 border">
                                    <i class="bi bi-car-front text-secondary me-2"></i>
                                    <span class="small fw-medium text-dark text-truncate">{{ $b->vehicle ? $b->vehicle->brand . ' ' . $b->vehicle->model : 'Unassigned' }}</span>
                                </div>
                                
                                <a href="{{ route('admin.booking.show', $b->id) }}" class="btn btn-sm btn-outline-secondary w-100 mt-3 border-dashed">
                                    View Manifest
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-4 bg-light rounded border border-dashed">
                            <i class="bi bi-cup-hot text-muted fs-1 mb-2 d-block"></i>
                            <p class="text-muted small fw-medium mb-0">No bookings scheduled for today.</p>
                        </div>
                    @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Area: Calendar -->
            <div class="col-lg-9 col-md-8 bg-light" id="calendar-col">
                <div id="calendar-container">
                    <div id='calendar'></div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="eventTitle">Package Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border"><i class="bi bi-person-fill text-muted me-1"></i> <span id="eventClient"></span></span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-people-fill text-muted me-1"></i> <span id="eventPax"></span> Pax</span>
                    </div>
                    
                    <div class="card bg-light border-0 shadow-sm mb-3">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center border shadow-sm me-3" style="width: 32px; height: 32px;">
                                    <i class="bi bi-calendar-check text-primary"></i>
                                </div>
                                <div>
                                    <div class="small text-muted fw-semibold" style="font-size: 0.7rem;">DATE & TIME</div>
                                    <div class="fw-bold" id="eventDateStr"></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center border shadow-sm me-3" style="width: 32px; height: 32px;">
                                    <i class="bi bi-car-front text-primary"></i>
                                </div>
                                <div>
                                    <div class="small text-muted fw-semibold" style="font-size: 0.7rem;">ASSIGNED VEHICLE</div>
                                    <div class="fw-bold" id="eventVehicle"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <a href="#" id="eventViewBtn" class="btn btn-primary fw-medium"><i class="bi bi-box-arrow-up-right me-1"></i> View Full Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: '100%', // Fills the container
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                themeSystem: 'bootstrap5',
                events: '/admin/calendar/events',
                eventClick: function(info) {
                    info.jsEvent.preventDefault(); // don't let the browser navigate
                    
                    const props = info.event.extendedProps;
                    
                    document.getElementById('eventTitle').innerText = info.event.title.split(' | ')[1];
                    document.getElementById('eventClient').innerText = props.client;
                    document.getElementById('eventPax').innerText = props.pax;
                    document.getElementById('eventDateStr').innerText = info.event.start.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' at ' + props.time;
                    document.getElementById('eventVehicle').innerText = props.vehicle;
                    
                    document.getElementById('eventViewBtn').href = '/admin/booking/' + props.booking_id;
                    
                    eventModal.show();
                },
                eventContent: function(arg) {
                    let divEl = document.createElement('div');
                    divEl.className = 'text-truncate px-1';
                    divEl.innerHTML = `<b>${arg.event.extendedProps.time}</b> ${arg.event.title.split(' | ')[1].substring(0, 15)}...`;
                    let arrayOfDomNodes = [ divEl ]
                    return { domNodes: arrayOfDomNodes }
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
