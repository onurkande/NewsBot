<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\RawTweetController;
use App\Http\Controllers\Admin\ScanHistoryController;
use App\Http\Controllers\Admin\SourceAccountController;
use App\Http\Controllers\Admin\SourceCategoryController;
use App\Http\Controllers\Admin\StoryClusterController;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
|
| Authentication and admin CRUD routes.
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => view('admin.index'))->name('dashboard');

    Route::resource('source-categories', SourceCategoryController::class)->except('show');
    Route::delete('source-categories/bulk-destroy', [SourceCategoryController::class, 'bulkDestroy'])
        ->name('source-categories.bulk-destroy');

    Route::resource('source-accounts', SourceAccountController::class)->except('show');
    Route::delete('source-accounts/bulk-destroy', [SourceAccountController::class, 'bulkDestroy'])
        ->name('source-accounts.bulk-destroy');
    Route::post('source-accounts/{source_account}/fetch', [SourceAccountController::class, 'fetch'])->name('source-accounts.fetch');

    Route::get('raw-tweets', [RawTweetController::class, 'index'])->name('raw-tweets.index');
    Route::get('story-clusters', [StoryClusterController::class, 'index'])->name('story-clusters.index');
    Route::get('story-clusters/{storyCluster}', [StoryClusterController::class, 'show'])->name('story-clusters.show');
    Route::get('scan-histories', [ScanHistoryController::class, 'index'])->name('scan-histories.index');
});
