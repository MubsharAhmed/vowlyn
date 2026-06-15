<?php

declare(strict_types=1);

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ServicesController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/services', ServicesController::class)->name('services');
Route::get('/marketplace', MarketplaceController::class)->name('marketplace');
Route::get('/portfolio', PortfolioController::class)->name('portfolio');
Route::get('/about', AboutController::class)->name('about');

Route::post('/contact', [ContactRequestController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('contact.store');
