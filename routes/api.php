<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/autoLogin', [LoginController::class, 'autoLogin']);

Route::post('/user/register', [UserController::class, 'create']);
Route::post('/artist/register', [ArtistController::class, 'create']);

Route::post('artist/img', [ArtistController::class, 'saveImg']);
Route::post('user/img', [UserController::class, 'saveImg'])->middleware('userAuth');

Route::post('/login', [LoginController::class, 'login']);

Route::get('/userEvents', [EventController::class, 'getEventsByUser'])->middleware('userAuth');
Route::get('/userArtists', [ArtistController::class, 'getArtistsByUser'])->middleware('userAuth');

Route::get('/event/{id}', [EventController::class, 'getEventById'])->middleware('userAuth');
