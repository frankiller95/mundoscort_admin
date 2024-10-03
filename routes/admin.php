<?php

use App\Http\Controllers\Admin\AnunciosController;
use App\Http\Controllers\Admin\PremiumController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Paypal\PayPalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/anuncio', [AnunciosController::class, 'index']);
    Route::post('/guardar_anuncio', [AnunciosController::class, 'createAnuncio']);
    Route::post('/actualizar_anuncio/{id}', [AnunciosController::class, 'updateAnuncio']);
    Route::get('/listar-anuncios', [AnunciosController::class, 'listarAnuncios']);
    Route::get('/anuncios/{id}/edit', [AnunciosController::class, 'edit'])->name('anuncios.edit');
    Route::post('/update_estado', [AnunciosController::class, 'changeEstadoAnuncio']);
    Route::post('/set_anuncio_premium', [AnunciosController::class, 'updateAnuncioPremium'])->name('set-anuncio-premium');
    Route::get('/comprar_premium', [PremiumController::class, 'index'])->name('premium.index');
    Route::post('/api/orders', [PayPalController::class, 'createOrder']);
    Route::post('/api/orders/{id}/capture', [PayPalController::class, 'captureOrder'])->name('set-capture-order');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/usuarios_registrados', [UsersController::class, 'index']);
});
