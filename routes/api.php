<?php

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



  Route::prefix('user')->group(
            function () {

   Route::prefix('auth')->group(
    function () {
        Route::post('signup', [App\Http\Controllers\UserAuthController::class, 'signup']);
        Route::post('login', [App\Http\Controllers\UserAuthController::class, 'login']);
    });
                
                Route::middleware(['auth:user'])->group(
                    function () {

                        Route::get('logout', [App\Http\Controllers\UserAuthController::class, 'logout']);
                        Route::get('profile', [App\Http\Controllers\UserAuthController::class, 'userProfile']);

                        Route::prefix('expense')->group(function () {
                            Route::post('/add', [App\Http\Controllers\ExpenseController::class, 'addExpense']);
                            Route::post('/update', [App\Http\Controllers\ExpenseController::class, 'updateExpense']);
                            Route::delete('delete/{id}', [App\Http\Controllers\ExpenseController::class, 'deleteSubject']);
                             Route::get('/list-expense', [App\Http\Controllers\ExpenseController::class, 'listExpense']);
                             Route::get('/show/{id}', [App\Http\Controllers\ExpenseController::class, 'showExpense']);

                            Route::post('/filter', [App\Http\Controllers\ExpenseController::class, 'filterExpense']);
                            Route::post('/import', [App\Http\Controllers\ExpenseController::class, 'importExpenses']);
                             Route::get('/to-reimburse', [App\Http\Controllers\ExpenseController::class, 'sumExpensesToReimburse']);

                        });

                    });

            });

  