<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\dashboard\ProfileController;
use App\Http\Controllers\dashboard\DashboardController;

/*
|--------------------------------------------------------------------------
| admin Routes
|--------------------------------------------------------------------------

*/
Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('auth')->group(function () {
        //dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
        Route::get('dashboard/reports', [DashboardController::class, 'reports']);

        //..........................................Profile Route.........................................................



        Route::resource('users', UserController::class);

    });
});
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('departments',DepartmentController::class)->except(['show']);
});
Route::middleware(['auth'])->group(function () {
    Route::resource('tasks', TaskController::class);
    Route::post('tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::get('tasks/mine', [TaskController::class, 'myTasks'])->name('tasks.mine');
});






