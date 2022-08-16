<?php

use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/files', [FileController::class, 'showAll']);
Route::get('/files/{filename}', [FileController::class, 'downloadFile'])->where('filename', '.*');
Route::post('/files', [FileController::class, 'saveFile']);
Route::post('/files/{path}', [FileController::class, 'saveFile']);
