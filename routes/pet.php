<?php

use App\Http\Controllers\PeopleController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetTypeController;
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

Route::group(['prefix' => 'pet', 'middleware' => 'auth:api'], function () {
    Route::post('get_pet', [PetController::class, 'getItem']);
    Route::get('search/{search}', [PetController::class, 'search']);
    Route::resources([
        'pet' => PetController::class,
    ]);
});

Route::group(['prefix' => 'pet_type', 'middleware' => 'auth:api'], function () {
    Route::get('search/{search}', [PetTypeController::class, 'search']);
});
