<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Protected routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Product View Routes (Accessible by all)
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show')->whereNumber('product');

    // Stock Transaction Routes (Accessible by all)
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/in', [StockController::class, 'inForm'])->name('in');
        Route::post('/in', [StockController::class, 'inStore'])->name('in.store');
        Route::get('/out', [StockController::class, 'outForm'])->name('out');
        Route::post('/out', [StockController::class, 'outStore'])->name('out.store');
        Route::get('/movements', [StockController::class, 'movements'])->name('movements');
        Route::get('/product/{product}', [StockController::class, 'getProduct'])->name('product');
    });

    // Reports (Accessible by all)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/send-telegram', [ReportController::class, 'sendToTelegram'])->name('send-telegram');
    });

    // ==========================================
    // ADMIN ONLY ROUTES
    // ==========================================
    Route::middleware(['is_admin'])->group(function () {

        // Product Master Data Management
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Categories (Admin Only)
        Route::resource('categories', CategoryController::class);

        // Stock Adjustments (Admin Only)
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/adjust', [StockController::class, 'adjustForm'])->name('adjust');
            Route::post('/adjust', [StockController::class, 'adjustStore'])->name('adjust.store');
        });

        // Settings (Admin Only)
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/telegram', [SettingController::class, 'telegram'])->name('telegram');
            Route::post('/telegram', [SettingController::class, 'telegramUpdate'])->name('telegram.update');
            Route::post('/telegram/set-webhook', [SettingController::class, 'setWebhook'])->name('telegram.webhook');
            Route::post('/telegram/test', [SettingController::class, 'testBot'])->name('telegram.test');
            Route::get('/general', [SettingController::class, 'general'])->name('general');
        });
    });

});
