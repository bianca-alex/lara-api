<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
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


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function(){
    Route::get('user',[AuthController::class, 'getUser']);
    Route::get('logout',[AuthController::class, 'logout']);

    Route::get('articles',[ArticleController::class, 'index']);
    Route::get('articles/{id}',[ArticleController::class, 'show']);
    Route::post('articles',[ArticleController::class, 'store']);
    Route::put('articles/{id}',[ArticleController::class, 'update']);
    Route::delete('articles/{id}',[ArticleController::class, 'delete']);
});
