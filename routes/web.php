<?php

use Illuminate\Support\Facades\Route;

// Redireccionar la raíz al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Redirección del dashboard principal según rol
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('secretaria')) {
            return redirect()->route('secretaria.dashboard');
        } elseif ($user->hasRole('cliente')) {
            return redirect()->route('cliente.dashboard');
        }
        
        return view('dashboard');
    })->name('dashboard');
});

// Rutas para Admin
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
    Route::get('/usuarios', function () { return view('admin.usuarios'); })->name('usuarios');
    Route::get('/canchas', function () { return view('admin.canchas'); })->name('canchas');
    Route::get('/clientes', function () { return view('admin.clientes'); })->name('clientes');
    Route::get('/reservas', function () { return view('admin.reservas'); })->name('reservas');
    Route::get('/reportes', function () { return view('admin.reportes'); })->name('reportes');
    
    // Registro de usuarios solo para admin
    Route::get('/register', function () { return view('auth.register'); })->name('register');
});

// Rutas para Secretaria
Route::middleware(['auth', 'verified', 'role:secretaria'])->prefix('secretaria')->name('secretaria.')->group(function () {
    Route::get('/dashboard', function () { return view('secretaria.dashboard'); })->name('dashboard');
    Route::get('/clientes', function () { return view('secretaria.clientes'); })->name('clientes');
    Route::get('/reservas', function () { return view('secretaria.reservas'); })->name('reservas');
    Route::get('/canchas', function () { return view('secretaria.canchas'); })->name('canchas');
});

// Rutas para Cliente
Route::middleware(['auth', 'verified', 'role:cliente'])->prefix('cliente')->name('cliente.')->group(function () {
    Route::get('/dashboard', function () { return view('cliente.dashboard'); })->name('dashboard');
    Route::get('/mis-reservas', function () { return view('cliente.mis-reservas'); })->name('mis-reservas');
    Route::get('/crear-reserva/{cancha_id?}', function ($cancha_id = null) { 
        return view('cliente.crear-reserva', compact('cancha_id')); 
    })->name('crear-reserva');
    Route::get('/canchas', function () { return view('cliente.canchas'); })->name('canchas');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
