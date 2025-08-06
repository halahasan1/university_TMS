<?php

use Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('adminPanel/register', Register::class)->name('register');


