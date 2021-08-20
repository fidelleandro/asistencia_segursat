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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');
Route::get('/reporte', [App\Http\Controllers\HomeController::class, 'reporte'])->name('reporte');
Route::get('/mi-reporte', [App\Http\Controllers\HomeController::class, 'personReport'])->name('personReport');
Route::get('/get-personal', [App\Http\Controllers\HomeController::class, 'getPeople'])->name('getPeople');
Route::get('/get-horarios', [App\Http\Controllers\HomeController::class, 'getSchedule'])->name('getSchedule');

Auth::routes();
/********************************************************************************************************************/
Route::get('/crear-privilegios', [App\Http\Controllers\CreateController::class,'crear_priv'])->name('crear_priv');
Route::get('/editar-privilegios', [App\Http\Controllers\CreateController::class,'edit_priv'])->name('edit_priv');
Route::get('/create-rolegroup', [App\Http\Controllers\CreateController::class,'Create_roleGroup'])->name('Create_roleGroup');
Route::get('/create-role', [App\Http\Controllers\CreateController::class,'Create_role'])->name('Create_role');
Route::get('/create-roleuser', [App\Http\Controllers\CreateController::class,'Create_roleUser'])->name('Create_roleUser');
Route::get('/create-people', [App\Http\Controllers\CreateController::class,'Create_people'])->name('Create_people');
Route::get('/create-user', [App\Http\Controllers\CreateController::class,'Create_user'])->name('Create_user');
Route::get('/create-peopleuser', [App\Http\Controllers\CreateController::class,'Create_peopleuser'])->name('Create_peopleuser');
Route::get('/create-staff', [App\Http\Controllers\CreateController::class,'Create_staff'])->name('Create_staff');
Route::get('/create-privilege', [App\Http\Controllers\CreateController::class,'Create_privilege'])->name('Create_privilege');
Route::get('/create-roleprivilege', [App\Http\Controllers\CreateController::class,'Create_role_privilege'])->name('Create_role_privilege');
Route::post('/crear-privilegio-user', [App\Http\Controllers\CreateController::class,'Crear_privilegio_user'])->name('Crear_privilegio_user');
