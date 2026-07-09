<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AppointmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Appointment::where('user_id', Auth::id());

        // Se l'utente ha scritto qualcosa nella barra di ricerca
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('titolo', 'LIKE', "%{$search}%");
        }

        // 2. NUOVO: Filtro per Data specifica
        if ($request->filled('data_filtro')) {
            $query->whereDate('data', $request->data_filtro);
        }

        // 3. NUOVO: Filtro per Stato (prenotato, completato, annullato)
        if ($request->filled('stato_filtro')) {
            $query->where('stato', $request->stato_filtro);
        }

        $appointments = $query  ->orderBy('data', 'asc')
                                ->orderBy('ora', 'asc')
                                ->get();

        return view('appointments.index', compact('appointments'));
    }

    /**
     * visualizza il form per la creazione di un nuovo appuntamento
     */
    public function create()
    {
        return view('appointments.create');
    }

    /**
     * memorizza il nuovo appuntamento nel database
     */
    public function store(Request $request)
    {
    // 1. Validazione base
    $request->validate([
        'titolo' => 'required|string|min:3|max:255',
        'solo_data' => 'required|date|after_or_equal:today',
        'solo_ora' => 'required',
        'descrizione' => 'nullable|string',
    ]);

    // 2. Controllo Appuntamenti uguali: cerchiamo solo tra gli appuntamenti del medico loggato
    $occupato = Appointment::where('user_id', Auth::id())
        ->where('data', $request->solo_data)
        ->where('ora', $request->solo_ora)
        ->where('stato', '!=', 'annullato')
        ->exists();

    if ($occupato) {
        // Messaggio di errore per il campo 'solo_ora'
        return redirect()->back()
            ->withInput()
            ->withErrors(['solo_ora' => 'L\'orario selezionato per questa data è già occupato da un altro paziente.']);
    }

    // 3. Creazione del nuovo appuntamento
    Appointment::create([
        'user_id'     => Auth::id(),
        'titolo'      => $request->titolo,
        'data'        => $request->solo_data,
        'ora'         => $request->solo_ora,
        'descrizione' => $request->descrizione,
        'stato'       => 'prenotato',
    ]);

    return redirect()->route('appointments.index')->with('success', 'Appuntamento prenotato con successo!');
    }

    /**
     * Form per modificare l'appuntamento
     */
    public function edit(Appointment $appointment)
    {
        $this->authorize('update', $appointment);
        return view('appointments.edit', compact('appointment'));
    }

    /**
     * Aggiorna l'appuntamento nel database.
     */
    public function update(Request $request, Appointment $appointment)
    {
    $this->authorize('update', $appointment);

    $request->validate([
        'titolo' => 'required|string|min:3|max:255',
        'solo_data' => 'required|date|after_or_equal:today',
        'solo_ora' => 'required',
        'stato' => 'required|in:prenotato,completato,annullato',
    ]);

    // Controllo appuntamenti uguali limitato al medico loggato
    $occupato = Appointment::where('user_id', Auth::id())
        ->where('data', $request->solo_data)
        ->where('ora', $request->solo_ora)
        ->where('stato', '!=', 'annullato')
        ->where('id', '!=', $appointment->id)
        ->exists();

    if ($occupato) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['solo_ora' => 'Impossibile spostare: l\'orario è già occupato.']);
    }

    $appointment->update([
        'titolo' => $request->titolo,
        'data' => $request->solo_data,
        'ora' => $request->solo_ora,
        'descrizione' => $request->descrizione,
        'stato' => $request->stato
    ]);

    return redirect()->route('appointments.index')->with('success', 'Modifica salvata!');
    }

    /**
     * Elimina l'appuntamento.
     */
    public function destroy(Appointment $appointment)
    {
        // 1. Controllo di sicurezza: l'appuntamento deve essere dell'utente loggato
        $this->authorize('delete', $appointment); // 
    
        $appointment->delete();
        
        // 3. RISPOSTA JSON (AJAX) messaggio di successo nella pagina
        return response()->json([
            'success' => true,
            'message' => 'Appuntamento eliminato correttamente'
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Controllo di sicurezza: solo il proprietario può cambiare lo stato
        $this->authorize('update', $appointment);

        // Validazione dello stato inviato
        $request->validate([
            'stato' => 'required|in:prenotato,completato,annullato',
        ]);

        // Aggiornamento nel database
        $appointment->update([
            'stato' => $request->stato
        ]);

        // Risposta JSON per JavaScript
        return response()->json([
            'success' => true,
            'message' => 'Stato aggiornato con successo'
        ]);
    }
}