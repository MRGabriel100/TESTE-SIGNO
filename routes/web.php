<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnqueteController;

Route::get('/', function () {
    return view('welcome');
});


// Painel de controle (CRUD)
Route::get('/painel', [EnqueteController::class, 'index'])->name('enquetes.index');
Route::post('/painel', [EnqueteController::class, 'store']);
Route::put('/painel/{id}', [EnqueteController::class, 'update'])->name('enquetes.update');
Route::delete('/painel/{id}', [EnqueteController::class, 'destroy'])->name('enquetes.destroy');

// Página de votação
Route::get('/enquete/{id}', [EnqueteController::class, 'mostrarEnquete'])->name('enquete.mostrar');

Route::post('/enquete/{id}/votar', [EnqueteController::class, 'votar'])->name('enquete.votar');