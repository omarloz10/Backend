<?php

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

Route::prefix("v1")
    ->group(function () {
        Route::get("/usuarios", [UserController::class, "index"]);
        Route::get("/usuarios/{id}", [UserController::class, "show"]);
        Route::post("/usuarios", [UserController::class, "store"]);
        Route::put("/usuarios/{id}", [UserController::class, "update"]);
        Route::patch("/usuarios/{id}", [UserController::class, "disable"]);
        Route::delete("/usuarios/{id}", [UserController::class, "destroy"]);

        Route::post("/login", [UserController::class, "login"]);
    });
