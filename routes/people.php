<?php

use App\Http\Controllers\PeopleController;
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

Route::group(['prefix' => 'people', 'middleware' => 'auth:api'], function () {
    Route::get('search/{search}', [PeopleController::class, 'search']);
    Route::get('get_for_type_people/{type_people_id}', [PeopleController::class, 'getForTypePeople']);   
});
