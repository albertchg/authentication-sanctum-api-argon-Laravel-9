<?php

use App\Http\Controllers\Api\AuthController;
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

/****************************** */
/*          authentication      */
/****************************** */
Route::post('register', [AuthController::class, 'register'])->name('registerUser');
Route::post('login', [AuthController::class, 'login'])->name('loginUser');

Route::group(['middleware' => ['auth:sanctum']], function () {
    /****************************** */
    /*          authentication      */
    /****************************** */
    Route::prefix('/auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user-Profile', [AuthController::class, 'userProfile']);
    });
});