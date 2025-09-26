<?php

use App\Http\Controllers\DocsController;
use Illuminate\Support\Facades\Route;

Route::view('/welcome', 'welcome');

Route::get('/', [DocsController::class, 'index'])->name('docs.home');
Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');
Route::get('/api-docs', [DocsController::class, 'index'])->name('docs.api');
Route::get('/openapi.yaml', [DocsController::class, 'openapi'])->name('docs.openapi');
