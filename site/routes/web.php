<?php

declare(strict_types=1);

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogFeedController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\ContentCreationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PerformanceMarketingController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WhyUsController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/services', [ServicesController::class, 'index'])->name('services');
Route::get('/services/{service}', [ServicesController::class, 'show'])
    ->where('service', '[a-z0-9-]+')
    ->name('services.show');

Route::get('/performance-marketing', PerformanceMarketingController::class)->name('performance-marketing');
Route::redirect('/marketplace', '/performance-marketing', 301)->name('marketplace');
Route::get('/portfolio', PortfolioController::class)->name('portfolio');
Route::get('/content-creation', ContentCreationController::class)->name('content-creation');
Route::get('/about', AboutController::class)->name('about');
Route::get('/why-us', WhyUsController::class)->name('why-us');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/feed.xml', BlogFeedController::class)->name('blog.feed');
Route::get('/blog/subscribe', [BlogController::class, 'subscribe'])->name('blog.subscribe');
Route::get('/blog/category/{category}', [BlogController::class, 'category'])
    ->where('category', '[a-z0-9-]+')
    ->name('blog.category');
Route::get('/blog/author/{author}', [BlogController::class, 'author'])
    ->where('author', '[a-z0-9-]+')
    ->name('blog.author');
Route::get('/blog/preview/{post}', [BlogController::class, 'preview'])
    ->middleware('signed')
    ->name('blog.preview');
Route::get('/blog/{slug}', [BlogController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('blog.show');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::post('/contact', [ContactRequestController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('contact.store');
