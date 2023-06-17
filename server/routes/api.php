<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// * USER ROUTES
Route::post("/v1/register",[UserController::class,"register"]);
Route::post("/v1/login",[UserController::class,"login"]);
Route::get("/v1/get-user-data",[UserController::class,"userData"]);

Route::post("/v1/auth-register",[UserController::class,"authRegister"]);
Route::post("/v1/auth-login",[UserController::class,"authLogin"]);

//  * ITEM ROUTES
Route::get("/v1/get-items",[ItemController::class,"getItems"]);
Route::post("/v1/get-item",[ItemController::class,"getItem"]);

// * REQUEST ROUTES
Route::post("/v1/get-all-requests",[RequestController::class,"getAllRequests"]);
Route::post("/v1/get-request-info",[RequestController::class,"getRequest"]);

// * SEARCH ROUTES
Route::post("/v1/search-items",[ItemController::class,"searchItems"]);


// * USER ONLY AUTHENTICATED ROUTES
Route::middleware(["auth:sanctum"])->group(function(){
    Route::post("/v1/logout",[UserController::class,"logout"]);

    // * PURCHASE ROUTES
    Route::post("/v1/make-purchase",[PurchaseController::class,"addPurchase"]);
    Route::post("/v1/get-user-purchases",[PurchaseController::class,"getUserPurchases"]);

    // * SELL ROUTES
    Route::post("/v1/sell-item",[ItemController::class,"sellItem"]);
    
    // * REQUEST ROUTES
    Route::post("/v1/add-request",[RequestController::class,"addRequest"]);
    Route::post("/v1/get-user-requests",[RequestController::class,"getUserRequests"]);

    
    
});



// todo: cut down on number revealed details and optimize queries