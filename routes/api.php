<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserArtistFollowController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserEventFollowController;
use App\Models\User;
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


//Login
Route::get('/autoLogin', [LoginController::class, 'autoLogin']);
Route::post('/login', [LoginController::class, 'login']);

//Register
Route::post('/user/register', [UserController::class, 'create']);
Route::post('/artist/register', [ArtistController::class, 'create']);

//Set profile image
Route::post('artist/img', [ArtistController::class, 'saveImg']);
Route::post('user/img', [UserController::class, 'saveImg'])->middleware('userAuth');

//Events
Route::get('/events', [EventController::class, 'getAllEvents'])->middleware('userAuth');
Route::get('/userEvents', [EventController::class, 'getEventsByUser'])->middleware('userAuth');
Route::get('/event/{id}', [EventController::class, 'getEventById'])->middleware('userAuth');

//Artists
Route::get('/artists', [ArtistController::class, 'getAllArtists'])->middleware('userAuth');
Route::get('/userArtists', [ArtistController::class, 'getArtistsByUser'])->middleware('userAuth');
Route::get('/artist/{userName}', [ArtistController::class, 'getArtistByUserName'])->middleware('userAuth');

//Event following status
Route::delete('/unfollowEvent/{id}', [UserEventFollowController::class, 'unfollowEvent'])->middleware('userAuth');
Route::get('/followEvent/{id}', [UserEventFollowController::class, 'followEvent'])->middleware('userAuth');

//Artist following status
Route::delete('/unfollowArtist/{id}', [UserArtistFollowController::class, 'unfollowArtist'])->middleware('userAuth');
Route::get('/followArtist/{id}', [UserArtistFollowController::class, 'followArtist'])->middleware('userAuth');

//Search
Route::get('/search', [SearchController::class, 'getResults'])->middleware('userAuth');

//Chats
Route::get('/eventUsers/{id}', [UserController::class, 'getUsersByEvent'])->middleware('userAuth');
