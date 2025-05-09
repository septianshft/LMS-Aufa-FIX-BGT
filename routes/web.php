<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseVideoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscribeTransactionController;
use App\Http\Controllers\TrainerController;
use App\Models\SubscribeTransaction;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'index'])->name('front.index');

Route::get('/details/{course:slug}', [FrontController::class, 'details'])->name('front.details');

Route::get('/category/{category:slug}', [FrontController::class, 'category'])->name('front.category');

Route::get('/pricing/{course:slug}', [FrontController::class, 'pricing'])->name('front.pricing');


Route::middleware('auth')->group(function () {
    Route::get('/profile', action: [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // must login
    Route::get('/checkout', action: [FrontController::class, 'chcekout'])->name('front.checkout')->middleware
    ('role:trainee');

    Route::post('/checkout/store', action: [FrontController::class, 'chcekout_store'])->name('front.checkout.store')->middleware
    ('role:trainee');

    Route::get('/learning/{course}/{courseVideoId}', [FrontController::class, 'learning'])->name('front.learning')
    ->middleware('role:trainee|trainer|admin');



    Route::prefix('admin')->name('admin.')->group(function(){
        Route::resource('categories', CategoryController::class)
        ->middleware('role:admin');

        Route::resource('trainers', TrainerController::class)
        ->middleware('role:admin');

        Route::resource('courses', CourseController::class)
        ->middleware('role:admin|trainer');

        Route::resource('subscribe_transactions', SubscribeTransactionController::class)
        ->middleware('role:admin');

        Route::get('/add/video/{course:id}', [CourseVideoController::class, 'create'])
        ->middleware('role:admin|trainer')
        ->name('course.add_video');

        Route::post('/add/video/save/{course:id}', [CourseVideoController::class, 'store'])
        ->middleware('role:admin|trainer')
        ->name('course.add_video.save');

        Route::resource('course_videos', CourseVideoController::class)
        ->middleware('role:admin|trainer');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});

require __DIR__.'/auth.php';
