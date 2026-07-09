<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <a href="{{ route('appointments.create') }}" class="btn btn-outline-dark btn-sl">
                + Nuovo Appuntamento
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if ($showWelcome)
                <div id="welcome-message" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-6 text-gray-900 border-l-4 border-success">
                        Benvenuto {{ Auth::user()->name }}! Hai effettuato l'accesso.
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <h3 class="text-success mb-1">AGENDA STUDIO MEDICO</h3>                    
                </div>

                <div id='calendar' class="p-2 border rounded bg-white"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestione inserimento e chiusura del messaggio di benvenuto
            const welcomeMsg = document.getElementById('welcome-message');
            if (welcomeMsg) {
                setTimeout(() => {
                    welcomeMsg.style.transition = "opacity 0.8s ease";
                    welcomeMsg.style.opacity = "0";
                    setTimeout(() => welcomeMsg.remove(), 800);
                }, 5000);
            }

            // Calendario
            var calendarEl = document.getElementById('calendar'); 

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'it',
                firstDay: 1,
                                
                headerToolbar: {
                    left: '',             
                    center: 'prev title next', 
                    right: ''                  
                },
                
                buttonText: {
                    today: 'Oggi'
                },

                events: @json($events), 
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

                eventClick: function(info) {
                    if (info.event.id) {
                        window.location.href = '/appointments/' + info.event.id + '/edit';
                    }
                },

                eventMouseEnter: function(info) {
                    info.el.style.cursor = 'pointer';
                }
            });
            calendar.render();
        });
    </script>
    
</x-app-layout>