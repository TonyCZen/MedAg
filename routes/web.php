<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Models\Appointment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Questa rotta recupera i dati per il calendario
Route::get('/dashboard', function () {
    $appointments = Appointment::where('user_id', Auth::id())->get();

    // Trasformiamo i dati nel formato "Eventi" che FullCalendar riconosce
    $events = $appointments->map(function ($app) {
        return [
            'id'    => $app->id,
            'title' => $app->titolo,
            'start' => $app->data . 'T' . $app->ora,
            'color' => $app->stato == 'completato' ? '#198754' : ($app->stato == 'annullato' ? '#dc3545' : '#ffc107'),
        ];
    });

    // Verifichiamo se l'utente è appena stato autenticato in questa sessione
    // Se è la prima volta che vede la dashboard in questa sessione, mostriamo il messaggio
    $showWelcome = false;
    if (!session()->has('welcome_shown')) {
        $showWelcome = true;
        session()->put('welcome_shown', true);
    }

    return view('dashboard', [
        'events' => $events,
        'showWelcome' => $showWelcome
    ]);
    
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Solo chi è loggato può gestire gli appuntamenti
Route::middleware(['auth'])->group(function () {
    Route::resource('appointments', AppointmentController::class);
});

// Questa rotta serve per l'aggiornamento parziale dello stato tramite AJAX
Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

require __DIR__.'/auth.php';