<?php

use App\Http\Controllers\StatistikController;
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

// Route::get('/', function () {
//     return view('index');
// });

// Route::get('/', function () {
//     return view('index');
// });

Route::get('/', function () {
     return view('home');
 });

route ::view('/statistik', 'statistik');
//Route::get('/', 'App\Http\Controllers\PagesController@home');

Route::get('/statistik', 'App\Http\Controllers\StatistikController@index')->name('mahasiswa');
Route::post('/statistik', 'App\Http\Controllers\StatistikController@store');

Route::get('/statistik/edit/{id_mahasiswa}', 'App\Http\Controllers\StatistikController@edit');
Route::put('/statistik/edit/{id_mahasiswa}', 'App\Http\Controllers\StatistikController@update');

Route::get('/statistik/delete/{id_mahasiswa}', 'App\Http\Controllers\StatistikController@delete');
// Route::get('/statistik/mahasiswaexport', 'App\Http\Controllers\StatistikController@mahasiswaexport')->name('export');

// Route::get('/frekuensi', 'App\Http\Controllers\StatistikController@frekuensi');
// Route::get('/statistik', 'App\Http\Controllers\StatistikController@statistik');

Route::get('/databergolong', 'App\Http\Controllers\StatistikController@databergolong');
Route::get('/chikuadrat', 'App\Http\Controllers\StatistikController@chikuadrat');
Route::get('/lilliefors', 'App\Http\Controllers\StatistikController@lilliefors');

Route::get('/export', 'App\Http\Controllers\StatistikController@export');       //route ke export
Route::post('/import', 'App\Http\Controllers\StatistikController@import');       //route ke export

Route::post('/statistik/ujiTBerkolerasi', 'App\Http\Controllers\StatistikController@storeX1X2');
Route::delete('/hapus/{id}', 'App\Http\Controllers\StatistikController@deleteT');
Route::get('/statistik/ujiTBerkolerasi', 'App\Http\Controllers\StatistikController@ujiTBerkolerasi');
Route::get('/exportujiT', 'App\Http\Controllers\StatistikController@ujiTBerkolerasiExport');
Route::post('/ujiTBerkolerasiImport', 'App\Http\Controllers\StatistikController@ujiTBerkolerasiImport');

Route::post('/statistik/ujiAnava', 'App\Http\Controllers\StatistikController@storeAnava');
Route::delete('/hapusAnava/{id}', 'App\Http\Controllers\StatistikController@deleteAnava');
Route::get('/statistik/ujiAnava', 'App\Http\Controllers\StatistikController@ujiAnava');
Route::get('/exportAnava', 'App\Http\Controllers\StatistikController@exportAnava');
Route::post('/importAnava', 'App\Http\Controllers\StatistikController@importAnava');