<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\{
    AuthController,
    BlogCategoryController,
    BlogController,
    CareerController,
};
use App\Models\BlogCategory;

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

Route::group(['prefix' => 'v1'], function() {

    Route::group(['prefix' => 'auth'], function() {

        Route::post('register', [AuthController::class, 'store']);
        Route::post('login', [AuthController::class, 'login']);

    });

    Route::group(['prefix' => 'career'], function() {

        Route::get('/', [CareerController::class, 'index']);
        Route::get('/{id}', [CareerController::class, 'show']);

    });

    Route::group(['prefix' => 'blog_category'], function() {

        Route::get('/', [BlogCategoryController::class, 'index']);
        Route::get('/{id}', [BlogCategoryController::class, 'show']);

    });

    Route::group(['prefix' => 'blog'], function() {

        Route::get('/', [BlogController::class, 'index']);
        Route::get('/{id}', [BlogController::class, 'show']);

    });

    Route::group(['middleware' => 'auth:sanctum'], function() {

        Route::get('/user', [AuthController::class, 'user']);

        Route::group(['prefix' => 'career'], function() {

            Route::post('/', [CareerController::class, 'store']);
            Route::put('/{id}', [CareerController::class, 'update']);
            Route::delete('/{id}', [CareerController::class, 'destroy']);

        });

        Route::group(['prefix' => 'blog_category'], function() {

            Route::post('/', [BlogCategoryController::class, 'store']);
            Route::put('/{id}', [BlogCategoryController::class, 'update']);
            Route::delete('/{id}', [BlogCategoryController::class, 'destroy']);

        });

        Route::group(['prefix' => 'blog'], function() {

            Route::post('/', [BlogController::class, 'store']);
            Route::put('/{id}', [BlogController::class, 'update']);
            Route::delete('/{id}', [BlogController::class, 'destroy']);

        });

    });

});
