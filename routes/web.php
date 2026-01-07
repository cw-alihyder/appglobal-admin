<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CKUploadImageController;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/upload-image', [CKUploadImageController::class, 'uploadImage'])->name('upload.image');
