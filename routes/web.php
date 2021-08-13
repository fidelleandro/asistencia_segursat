<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/reporte', [App\Http\Controllers\HomeController::class, 'reporte'])->name('reporte');
Route::get('/reporte-personal', [App\Http\Controllers\HomeController::class, 'personReport'])->name('personReport');
Route::get('/get-personal', [App\Http\Controllers\HomeController::class, 'getPeople'])->name('getPeople');
Route::get('/get-horarios', [App\Http\Controllers\HomeController::class, 'getSchedule'])->name('getSchedule');
