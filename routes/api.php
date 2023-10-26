<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Post\ShowController;
use App\Http\Controllers\Post\ShowChildsController;
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
Route::middleware('auth:sanctum')->get('/authenticated-endpoint', function () {
    return 'Authenticated!';
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/posts', ShowController::class)->name('show');
Route::post('/post/childs/{post}', ShowChildsController::class)->name('showChilds');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['namespace' => 'App\Http\Controllers\Post', 'prefix' => 'post'], function () {
            Route::get('/get/{id}', IndexController::class);
            Route::post('/', StoreController::class);
            Route::patch('/{post}', UpdateController::class);
            Route::delete('/{post}', DeleteController::class);
        }
    );
    Route::group(['namespace' => 'App\Http\Controllers\Avatar', 'prefix' => 'avatar'], function () { 
            Route::post('/', StoreController::class);
            Route::delete('/{post}', DeleteController::class);
        }
    );
    Route::group(['namespace' => 'App\Http\Controllers\File', 'prefix' => 'file'], function () {
            Route::post('/', StoreController::class);
            Route::delete('/{file}', DeleteController::class);
        }
    );
});
