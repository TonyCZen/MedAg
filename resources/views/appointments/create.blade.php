<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="POST" action="{{ route('appointments.store') }}" id="appointmentForm">
                    @csrf 

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="titolo" class="form-label fw-bold">Paziente / Motivo Visita</label>
                            <input type="text" name="titolo" id="titolo" class="form-control" 
                                   placeholder="Es: Mario Rossi - Controllo periodico" 
                                   minlength="3" value="{{ old('titolo') }}" required>
                            <x-input-error :messages="$errors->get('titolo')" class="mt-2" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="solo_data" class="form-label fw-bold">Data</label>
                            <input type="date" name="solo_data" id="solo_data" class="form-control" 
                                   min="{{ date('Y-m-d') }}" 
                                   value="{{ old('solo_data', date('Y-m-d')) }}" required>
                            <x-input-error :messages="$errors->get('solo_data')" class="mt-2" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="solo_ora" class="form-label fw-bold">Orario</label>
                            
                            <select name="solo_ora" id="solo_ora" 
                                    class="form-select @error('solo_ora') is-invalid @enderror" required>
                                
                                @php
                                    // Forza il fuso orario italiano per il calcolo dell'ora attuale
                                    $oraAdesso = \Carbon\Carbon::now('Europe/Rome')->format('H:i');
                                    $oggi = \Carbon\Carbon::today()->format('Y-m-d');
                                    
                                    // Valori scelti dall'utente prima dell'errore di validazione
                                    $currentOra = old('solo_ora');
                                    $dataSelezionata = old('solo_data', $oggi);
                                @endphp

                                @foreach(range(8, 19) as $ora)
                                    @foreach(['00', '15', '30', '45'] as $minuto)
                                        @php 
                                            $orarioOpzione = sprintf('%02d:%s', $ora, $minuto); 
                                            
                                            // Escludere gli orari passati rispetto all'ora italiana
                                            $isPassato = ($dataSelezionata == $oggi && $orarioOpzione < $oraAdesso);
                                        @endphp

                                        @if(!$isPassato)
                                            <option value="{{ $orarioOpzione }}" {{ $currentOra == $orarioOpzione ? 'selected' : '' }}>
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
                            <textarea name="descrizione" id="descrizione" rows="4" class="form-control" placeholder="Dettagli sulla visita...">{{ old('descrizione') }}</textarea>
                            <x-input-error :messages="$errors->get('descrizione')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                            Annulla
                        </a>

                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            Salva Appuntamento
                        </button>
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
        select.classList.remove('border-success', 'border-warning', 'text-success', 'text-warning');
        if (select.value === 'confermato') select.classList.add('border-success', 'text-success');
        else if (select.value === 'in_attesa') select.classList.add('border-warning', 'text-warning');
        }

        function filterTimes() {
            const selectedDate = dateInput.value;
            const today = new Date().toISOString().split('T')[0];
            
            // Ora e minuti attuali
            const now = new Date();
            const currentTime = now.getHours().toString().padStart(2, '0') + ":" + 
                                now.getMinutes().toString().padStart(2, '0');

            options.forEach(option => {
                if (selectedDate === today) {
                    // Nascondi orari passati
                    if (option.getAttribute('data-time') < currentTime) {
                        option.style.display = 'none';
                        if (option.selected) timeSelect.value = ""; 
                    } else {
                        option.style.display = 'block';
                    }
                } else {
                    // Se è un giorno futuro, mostra tutto
                    option.style.display = 'block';
                }
            });
        }

        // Esegui al cambio data e al caricamento pagina
        dateInput.addEventListener('change', filterTimes);
        window.addEventListener('load', filterTimes);
    </script>
</x-app-layout>