<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\RawTweetController;
use App\Http\Controllers\Admin\SourceAccountController;
use App\Http\Controllers\Admin\SourceCategoryController;
use App\Http\Controllers\Admin\StoryClusterController;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin panel routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "admin" middleware group.
|
*/

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    })->name('dashboard');

    Route::resource('source-categories', SourceCategoryController::class)->except('show');
     Route::delete('source-categories/bulk-destroy', [SourceCategoryController::class, 'bulkDestroy'])
        ->name('source-categories.bulk-destroy');
    
    Route::resource('source-accounts', SourceAccountController::class)->except('show');
    Route::post('source-accounts/{source_account}/fetch', [SourceAccountController::class, 'fetch'])->name('source-accounts.fetch');
    Route::get('raw-tweets', [RawTweetController::class, 'index'])->name('raw-tweets.index');
    Route::get('story-clusters', [StoryClusterController::class, 'index'])->name('story-clusters.index');
});
