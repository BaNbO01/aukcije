<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');





Route::middleware('auth:sanctum')->group(function () {
    
Route::apiResource('categories', CategoryController::class);

Route::put('/auctions/{id}', [AuctionController::class, 'update']);
Route::get('/auctions', [AuctionController::class, 'filterAuctions']);
Route::put('/auctions/{id}/close', [AuctionController::class, 'closeAuction']);
Route::delete('/auctions/{id}', [AuctionController::class, 'destroy']);
Route::post('/auctions', [AuctionController::class, 'store']);

Route::post('/auctions/{auctionId}/place-offer', [AuctionController::class, 'placeOffer']);

Route::get('/users',[UserController::class,'index']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::get('/users/{id}/auctions', [UserController::class, 'getUserAuctions']);
Route::get('/user/won-auctions', [UserController::class, 'getUserWonAuctions']);
Route::get('/users/participated', [UserController::class, 'getUserParticipatedAuctions']);



});



