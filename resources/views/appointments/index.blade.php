<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestione Appuntamenti
            </h2>
            <a href="{{ route('appointments.create') }}" class="btn btn-outline-dark btn-sl">
                + Nuovo Appuntamento
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-1">
                    <h3 class="text-danger mb-1">Agenda Studio Medico</h3>

                    <form action="{{ route('appointments.index') }}" method="GET" 
                        class="row g-2 px-0 py-1 align-items-center" 
                        style="min-height: 60px;"> 
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold text-muted mb-0" style="font-size: 0.75rem;">Cerca Paziente</label>
                            <input type="text" name="search" class="form-control form-control-sm shadow-none" 
                                placeholder="Nome..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold text-muted mb-0" style="font-size: 0.75rem;">Filtra Data</label>
                            <input type="date" name="data_filtro" class="form-control form-control-sm shadow-none" 
                                value="{{ request('data_filtro') }}">
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label small fw-bold text-muted mb-0" style="font-size: 0.75rem;">Filtra Stato</label>
                            <select name="stato_filtro" class="form-select form-select-sm shadow-none">
                                <option value="">Tutti</option>
                                <option value="prenotato" {{ request('stato_filtro') == 'prenotato' ? 'selected' : '' }}>Prenotato</option>
                                <option value="completato" {{ request('stato_filtro') == 'completato' ? 'selected' : '' }}>Completato</option>
                                <option value="annullato" {{ request('stato_filtro') == 'annullato' ? 'selected' : '' }}>Annullato</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex gap-1 align-self-end mb-4"> 
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1" style="height: 32px;">CERCA</button>
                            @if(request('search') || request('data_filtro') || request('stato_filtro'))
                                <a href="{{ route('appointments.index') }}" 
                                class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center" 
                                style="width: 32px; height: 32px; font-style: normal; font-weight: bold; line-height: ;" 
                                title="Resetta">
                                &times;
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Titolo</th>
                                <th>Data</th>
                                <th>Ora</th>
                                <th>Descrizione</th>
                                <th>Stato</th> 
                                <th class="text-end">Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appointment)
                                <tr id="appointment-row-{{ $appointment->id }}">
                                    <td class="fw-bold text-primary">{{ $appointment->titolo }}</td>
                                    
                                    <td>
                                        <i class="bi bi-calendar3 me-1 text-muted"></i>
                                        {{ \Carbon\Carbon::parse($appointment->data)->format('d/m/Y') }}
                                    </td>
                                    
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ \Carbon\Carbon::parse($appointment->ora)->format('H:i') }}
                                        </span>
                                    </td>

                                    <td class="text-muted small">
                                        {{ Str::limit($appointment->descrizione, 40) }}
                                    </td>
                                    
                                    <td>
                                        <select onchange="updateStatus({{ $appointment->id }}, this.value)" 
                                                id="status-select-{{ $appointment->id }}"
                                                class="form-select form-select-sm {{ $appointment->stato == 'completato' ? 'border-success text-success' : ($appointment->stato == 'annullato' ? 'border-danger text-danger' : 'border-warning text-warning') }}" 
                                                style="width: auto;">
                                            <option value="prenotato" {{ $appointment->stato == 'prenotato' ? 'selected' : '' }}>Prenotato</option>
                                            <option value="completato" {{ $appointment->stato == 'completato' ? 'selected' : '' }}>Completato</option>
                                            <option value="annullato" {{ $appointment->stato == 'annullato' ? 'selected' : '' }}>Annullato</option>
                                        </select>
                                    </td>

                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            @can('update', $appointment)
                                                <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-outline-warning btn-sm">
                                                    Modifica
                                                </a>
                                            @endcan

                                            @can('delete', $appointment)
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm" 
                                                        onclick="deleteAppointment({{ $appointment->id }})">
                                                    Elimina
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Non ci sono appuntamenti in agenda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Logica JavaScript per il cambio stato (Fetch API)
        function updateStatus(id, nuovoStato) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const selectElement = document.getElementById(`status-select-${id}`);

            fetch(`/appointments/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ stato: nuovoStato })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    selectElement.classList.remove('border-success', 'border-warning', 'border-danger', 'text-success', 'text-warning', 'text-danger');
                    
                    if(nuovoStato === 'completato') selectElement.classList.add('border-success', 'text-success');
                    else if(nuovoStato === 'annullato') selectElement.classList.add('border-danger', 'text-danger');
                    else selectElement.classList.add('border-warning', 'text-warning');
                    
                    console.log('Stato aggiornato con successo');
                } else {
                    alert('Errore durante l\'aggiornamento: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Errore di connessione.');
            });
        }

        // Funzione Elimina
        function deleteAppointment(id) {
            if (!confirm('Vuoi davvero eliminare questo appuntamento?')) return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/appointments/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 1. Animazione e rimozione riga
                    const row = document.getElementById(`appointment-row-${id}`);
                    row.style.transition = "opacity 0.5s ease";
                    row.style.opacity = "0";
                    
                    setTimeout(() => {
                        row.remove();
                        
                        // 2. Creazione dinamica del messaggio
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert" id="dynamic-alert">
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                        
                        // Inseriamo l'alert sopra la tabella
                        const tableContainer = document.querySelector('.table-responsive');
                        tableContainer.insertAdjacentHTML('beforebegin', alertHtml);

                        // 3. Autochiusura dopo 5 secondi
                        setTimeout(() => {
                            const dynamicAlert = document.getElementById('dynamic-alert');
                            if(dynamicAlert) {
                                dynamicAlert.style.transition = "opacity 0.5s ease";
                                dynamicAlert.style.opacity = "0";
                                setTimeout(() => dynamicAlert.remove(), 500);
                            }
                        }, 5000);

                    }, 500);
                } else {
                    alert('Errore: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Errore durante la fetch:', error);
                alert('Si è verificato un errore di connessione.');
            });
        }

        // Gestione chiusura automatica e inserimento del messaggio di successo
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                setTimeout(() => {
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 500);
                }, 5000);

                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        alert.remove();
                    });
                }
            }
        });
    </script>
</x-app-layout>