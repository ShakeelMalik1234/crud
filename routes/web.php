<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::get('signup', function () {
    return view('welcome');
});

Route::post('ragistration',[UserController::class, 'userStore'])->name('user-ragistration');
Route::post('log-in',[UserController::class, 'userLogin'])->name('user-login');
Route::get('login',[UserController::class, 'formLogin'])->name('form-login');
Route::group(['middleware' => 'web'],function(){
    Route::get('dashboard',[UserController::class, 'userDashboard'])->name('user-dashboard');
    Route::get('signout',[UserController::class, 'userSignout'])->name('user-signout');
});
