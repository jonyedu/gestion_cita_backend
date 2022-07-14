<?php

use App\Http\Controllers\PeopleController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetMedicalAppointmentController;
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

Route::group(['prefix' => 'appointment_medical', 'middleware' => 'auth:api'], function () {
    Route::post('get_appointment_medical', [PetMedicalAppointmentController::class, 'getItem']);

    Route::resources([
        'appointment_medical' => PetMedicalAppointmentController::class,
    ]);
});
