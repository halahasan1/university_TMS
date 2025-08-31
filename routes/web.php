<?php

use App\Models\User;
use Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Route;
use Filament\Notifications\Notification;

Route::get('/', function () {
    return view('welcome');
});
Route::get('adminPanel/register', Register::class)->name('register');

Route::get('/test-note', function () {
    $u = User::where('email','super@admin.com')->first();
    Notification::make()
        ->title('Hello from web')
        ->body('Inside Filament request')
        ->success()
        ->sendToDatabase($u);
    return 'ok';
})->middleware(['web']);

