<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
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
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'customer'
], function ($router) {
    Route::post('/store', [CustomerController::class, 'store']); //customer add to database
    Route::get('/', [CustomerController::class, 'index']); //customers created by logged user
    Route::get('/{id}', [CustomerController::class, 'show']); //customer with id {}
    Route::put('/{id}', [CustomerController::class, 'update']); //data edit
    Route::delete('/{id}', [CustomerController::class, 'destroy']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'invoices'
], function ($router) {
    Route::post('/', [InvoiceController::class, 'store']); 
    Route::get('/', [InvoiceController::class, 'index']);
    Route::get('/{id}', [InvoiceController::class, 'show']);
    Route::put('/{id}', [InvoiceController::class, 'update']);
    Route::delete('/{id}', [InvoiceController::class, 'destroy']);
    Route::get('pdf/{id}', [InvoiceController::class, 'downloadPDF']);
});


/*
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
