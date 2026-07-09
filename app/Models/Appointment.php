<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    // Permette solo agli utenti loggati di modificare i dati
    protected $fillable = ['titolo', 'descrizione', 'data', 'ora', 'descrizione', 'stato', 'user_id'];
}