<?php

use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\Register;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Profile;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 *
 * App Routes
 */
Route::middleware('auth')->group(function () {
    Route::redirect('/', 'dashboard');
    Route::get('/dashboard', Dashboard::class);
    Route::get('/profile', Profile::class);
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('auth.login');
    Route::get('/register', Register::class)->name('auth.register');

    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/login');
    })->name('logout');
});

//you can customize the ->layout('layouts.base');
//
//Route::get('/register', function () {
//    return view('auth.register');
//});
