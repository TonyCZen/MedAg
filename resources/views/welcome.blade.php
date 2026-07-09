<?php
// LOGICA DI REINDIRIZZAMENTO
// Se l'utente è loggato, va diretto all'agenda senza vedere questa pagina
if (auth()->check()) {
    header("Location: " . route('appointments.index'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MedAg - Gestione Appuntamenti</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body class="welcome-screen">

    <div class="login-card">
        
        <div class="mb-4">
            <i class="bi bi-heart-pulse-fill app-logo"></i>
            <div class="app-name">MedAg</div>
        </div>

        <h1 class="main-title">
            Il tuo tempo<br>gestito al meglio.
        </h1>

        <p class="description">
            L'agenda digitale veloce per gestire gli appuntamenti dei tuoi pazienti senza stress. Organizza le visite in pochi secondi.
        </p>

        <div class="d-grid gap-2">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-register shadow-sm">
                    Crea Account
                </a>
            @endif

            <a href="{{ route('login') }}" class="btn-login">
                Accedi
            </a>
        </div>

        <div class="features-row">
            <div class="feature-item">
                <i class="bi bi-shield-check text-success"></i>
                Dati al sicuro
            </div>
            <div class="feature-item">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
                Inserimento istantaneo
            </div>
        </div>

    </div>

</body>
</html>