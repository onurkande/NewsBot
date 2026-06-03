<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AiGenerationController;
use App\Http\Controllers\Admin\AiQueueController;
use App\Http\Controllers\Admin\AiSettingController;
use App\Http\Controllers\Admin\PoolHistoryController;
use App\Http\Controllers\Admin\PoolSelectionController;
use App\Http\Controllers\Admin\PoolSettingController;
use App\Http\Controllers\Admin\PromptController;
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
    Route::get('raw-tweets/{rawTweet}', [RawTweetController::class, 'show'])->name('raw-tweets.show');
    Route::get('story-clusters', [StoryClusterController::class, 'index'])->name('story-clusters.index');
    Route::get('story-clusters/{storyCluster}', [StoryClusterController::class, 'show'])->name('story-clusters.show');
    Route::get('scan-histories', [ScanHistoryController::class, 'index'])->name('scan-histories.index');

    // Havuz Yonetimi
    Route::get('pool-settings', [PoolSettingController::class, 'edit'])->name('pool-settings.edit');
    Route::put('pool-settings', [PoolSettingController::class, 'update'])->name('pool-settings.update');

    Route::get('pool-selection', [PoolSelectionController::class, 'index'])->name('pool-selection.index');

    Route::get('pool-history', [PoolHistoryController::class, 'index'])->name('pool-history.index');
    Route::get('pool-history/{poolBatch}', [PoolHistoryController::class, 'show'])->name('pool-history.show');

    // AI Yonetimi
    Route::get('ai-settings', [AiSettingController::class, 'edit'])->name('ai-settings.edit');
    Route::put('ai-settings', [AiSettingController::class, 'update'])->name('ai-settings.update');
    Route::post('ai-settings/test', [AiSettingController::class, 'test'])->name('ai-settings.test');

    Route::resource('prompts', PromptController::class)->except('show');
    Route::delete('prompts/bulk-destroy', [PromptController::class, 'bulkDestroy'])
        ->name('prompts.bulk-destroy');
    Route::post('prompts/{prompt}/activate', [PromptController::class, 'activate'])->name('prompts.activate');
    Route::post('prompts/{prompt}/deactivate', [PromptController::class, 'deactivate'])->name('prompts.deactivate');

    Route::get('ai-queue', [AiQueueController::class, 'index'])->name('ai-queue.index');
    Route::get('ai-queue/{aiQueue}', [AiQueueController::class, 'show'])->name('ai-queue.show');

    Route::get('ai-generations', [AiGenerationController::class, 'index'])->name('ai-generations.index');
    Route::get('ai-generations/{aiGeneration}', [AiGenerationController::class, 'show'])->name('ai-generations.show');
    Route::post('ai-generations/{aiGeneration}/approve', [AiGenerationController::class, 'approve'])->name('ai-generations.approve');
    Route::post('ai-generations/{aiGeneration}/reject', [AiGenerationController::class, 'reject'])->name('ai-generations.reject');
    Route::post('ai-generations/{aiGeneration}/publish', [AiGenerationController::class, 'publish'])->name('ai-generations.publish');

    // Twscrape Yönetimi
    Route::prefix('twscrape')->name('twscrape.')->group(function () {
        Route::get('accounts', [\App\Http\Controllers\Admin\Twscrape\TwscrapeAccountController::class, 'index'])->name('accounts.index');
        Route::get('accounts/{username}', [\App\Http\Controllers\Admin\Twscrape\TwscrapeAccountController::class, 'show'])->name('accounts.show');
        Route::post('accounts/{username}/toggle', [\App\Http\Controllers\Admin\Twscrape\TwscrapeAccountController::class, 'toggle'])->name('accounts.toggle');
        Route::put('accounts/{username}/weight', [\App\Http\Controllers\Admin\Twscrape\TwscrapeAccountController::class, 'updateWeight'])->name('accounts.weight');
        
        Route::get('stats', [\App\Http\Controllers\Admin\Twscrape\TwscrapeStatController::class, 'index'])->name('stats.index');
        
        Route::get('commands', [\App\Http\Controllers\Admin\Twscrape\TwscrapeCommandController::class, 'index'])->name('commands.index');
        Route::post('commands', [\App\Http\Controllers\Admin\Twscrape\TwscrapeCommandController::class, 'execute'])->name('commands.execute');
        
        Route::get('health', [\App\Http\Controllers\Admin\Twscrape\TwscrapeHealthController::class, 'index'])->name('health.index');
        
        Route::get('logs', [\App\Http\Controllers\Admin\Twscrape\TwscrapeLogController::class, 'index'])->name('logs.index');
    });
});
