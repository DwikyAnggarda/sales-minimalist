<?php

use App\Livewire\DailySales;
use App\Livewire\ProductMasterData;
use App\Livewire\SalesDashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', SalesDashboard::class)->name('dashboard');
    Route::get('products', ProductMasterData::class)->name('products');
    Route::get('daily-sales', DailySales::class)->name('daily-sales');
});

require __DIR__.'/settings.php';
