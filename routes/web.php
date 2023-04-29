<?php

use Illuminate\Support\Facades\Auth;
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

Route::group(['prefix' => 'clients'], function () {
    Route::get('/', [\App\Http\Controllers\ClientsController::class,'index'])->name('client.index');
    Route::get('/create', [\App\Http\Controllers\ClientsController::class,'create'])->name('client.create');
    Route::post('/importCSV', [\App\Http\Controllers\ClientsController::class,'importCsv'])->name('client.import_csv');
    Route::get('/filter', [\App\Http\Controllers\ClientsController::class,'clientFilter'])->name('client.filter');
    Route::post('/exportCSV', [\App\Http\Controllers\ClientsController::class,'exportCSV'])->name('client.export');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
