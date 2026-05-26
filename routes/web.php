<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Site\IndexController;



Route::get('/', [IndexController::class, 'index'])->name('index');



require __DIR__.'/admin.php';