<?php

use App\Http\Controllers\DistanceController;
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

Route::get('/', [DistanceController::class, 'index'])->name('jarak');
Route::post('/', [DistanceController::class, 'hitungJarak'])
    ->name('hitung-jarak')
    ->middleware('preventDirectAccess');
