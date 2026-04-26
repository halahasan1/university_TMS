<?php

use App\Http\Controllers\StudentReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'Test route under admin panel.';
});
Route::post('/courses/{course}/reviews', [StudentReviewController::class, 'store'])
    ->name('courses.reviews.store');
