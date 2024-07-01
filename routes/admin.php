<?php

use App\Http\Controllers\Admin\AnunciosController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/anuncio', [AnunciosController::class, 'index']);
    Route::post('/guardar_anuncio', [AnunciosController::class, 'createAnuncio']);
    Route::post('/actualizar_anuncio/{id}', [AnunciosController::class, 'updateAnuncio']);
    Route::get('/listar-anuncios', [AnunciosController::class, 'listarAnuncios']);
    Route::get('/anuncios/{id}/edit', [AnunciosController::class, 'edit'])->name('anuncios.edit');
    Route::post('/update_estado', [AnunciosController::class, 'changeEstadoAnuncio']);
});
