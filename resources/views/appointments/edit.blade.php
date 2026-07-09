<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="POST" action="{{ route('appointments.update', $appointment->id) }}" id="editAppointmentForm">
                    @csrf 
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="titolo" class="form-label fw-bold">Paziente / Motivo Visita</label>
                            <input type="text" name="titolo" id="titolo" class="form-control" 
                                   minlength="3" value="{{ old('titolo', $appointment->titolo) }}" required>
                            <x-input-error :messages="$errors->get('titolo')" class="mt-2" />
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="solo_data" class="form-label fw-bold">Data</label>
                            <input type="date" name="solo_data" id="solo_data" class="form-control" 
                                min="{{ date('Y-m-d') }}" 
                                value="{{ old('solo_data', $appointment->data) }}" required>
                            <x-input-error :messages="$errors->get('solo_data')" class="mt-2" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="solo_ora" class="form-label fw-bold">Orario</label>
                            
                            <select name="solo_ora" id="solo_ora" 
                                    class="form-select @error('solo_ora') is-invalid @enderror" required>
                                
                                @php
                                    $oraAdesso = \Carbon\Carbon::now('Europe/Rome')->format('H:i');
                                    $oggi = \Carbon\Carbon::today()->format('Y-m-d');
                                    
                                    // 1. Recuperiamo l'ora salvata nel database (es. 11:00:00 -> 11:00)
                                    $savedOra = \Carbon\Carbon::parse($appointment->ora)->format('H:i');
                                    
                                    // 2. Valore da selezionare: o quello vecchio del DB o quello appena scelto se c'è un errore
                                    $currentOra = old('solo_ora', $savedOra);
                                    
                                    // 3. Data attuale per il calcolo iniziale
                                    $dataSelezionata = old('solo_data', $appointment->data);
                                @endphp

                                @foreach(range(8, 19) as $ora)
                                    @foreach(['00', '15', '30', '45'] as $minuto)
                                        @php 
                                            $orarioOpzione = sprintf('%02d:%s', $ora, $minuto); 
                                            $isPassato = ($dataSelezionata == $oggi && $orarioOpzione < $oraAdesso);
                                        @endphp

                                        {{-- Mostrare l'opzione se non è passata o se è quella già salvata nel DB --}}
                                        @if(!$isPassato || $orarioOpzione == $savedOra)
                                            <option value="{{ $orarioOpzione }}" 
                                                    data-time="{{ $orarioOpzione }}" 
                                                    {{ $currentOra == $orarioOpzione ? 'selected' : '' }}>
                                                {{ $orarioOpzione }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endforeach
                            </select>

                            <x-input-error :messages="$errors->get('solo_ora')" class="mt-2" />
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="descrizione" class="form-label fw-bold">Note aggiuntive</label>
                            <textarea name="descrizione" id="descrizione" rows="4" class="form-control">{{ old('descrizione', $appointment->descrizione) }}</textarea>
                            <x-input-error :messages="$errors->get('descrizione')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                            Annulla
                        </a>

                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <label for="stato" class="form-label mb-0 fw-bold small text-muted">Stato:</label>
                                <select name="stato" id="stato_edit" class="form-select form-select-sm fw-bold" 
                                        style="width: auto; border-width: 2px;" onchange="coloraStato(this)">
                                    <option value="prenotato" class="text-warning" {{ $appointment->stato == 'prenotato' ? 'selected' : '' }}>Prenotato</option>
                                    <option value="completato" class="text-success" {{ $appointment->stato == 'completato' ? 'selected' : '' }}>Completato</option>
                                    <option value="annullato" class="text-danger" {{ $appointment->stato == 'annullato' ? 'selected' : '' }}>Annullato</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary px-4 fw-bold">
                                Salva Modifiche
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        const dateInput = document.getElementById('solo_data');
        const timeSelect = document.getElementById('solo_ora');
        const options = timeSelect.querySelectorAll('option[data-time]');

        function coloraStato(select) {
        select.classList.remove('border-success', 'border-warning', 'border-danger', 'text-success', 'text-warning', 'text-danger');
        if (select.value === 'completato') select.classList.add('border-success', 'text-success');
        else if (select.value === 'prenotato') select.classList.add('border-warning', 'text-warning');
        else if (select.value === 'annullato') select.classList.add('border-danger', 'text-danger');
        }
        // Esegui al caricamento per colorare lo stato attuale
        window.addEventListener('load', () => coloraStato(document.getElementById('stato_edit')));

        function filterTimes() {
            const selectedDate = dateInput.value;
            const today = new Date().toISOString().split('T')[0];
            
            const now = new Date();
            const currentTime = now.getHours().toString().padStart(2, '0') + ":" + 
                                now.getMinutes().toString().padStart(2, '0');

            options.forEach(option => {
                if (selectedDate === today) {
                    if (option.getAttribute('data-time') < currentTime) {
                        option.style.display = 'none';
                        // Se l'orario precedentemente salvato è nel passato ma la data è oggi, 
                        // lo lasciamo visibile solo se è quello già selezionato, altrimenti lo nascondiamo.
                        if (option.selected && option.getAttribute('data-time') < currentTime) {
                            option.style.display = 'block'; 
                        }
                    } else {
                        option.style.display = 'block';
                    }
                } else {
                    option.style.display = 'block';
                }
            });
        }

        dateInput.addEventListener('change', filterTimes);
        window.addEventListener('load', filterTimes);
    </script>
</x-app-layout>