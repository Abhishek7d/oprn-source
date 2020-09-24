<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WebsiteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/resend', [UserController::class, 'resend']);
Route::post('/logout', [UserController::class, 'logout']);
Route::get('email/verify/{id}', [UserController::class, 'verify'])->name('verification.verify');
Route::post('/reset', [UserController::class, 'reset']);
Route::post('/reset/password', [UserController::class, 'resetPassword']);
Route::get('password/reset/{token}', [UserController::class, 'returnToFrontEnd'])->name('password.reset');

Route::get('completed/{server_id}/{server_hash}', [DashboardController::class, 'serverCompleted']);

Route::group(['middleware' => 'checkAuth'], function () {
    Route::get('/sizes', [DashboardController::class, 'availableSizes']);
    Route::post('/droplet', [DashboardController::class, 'createDroplet']);
    Route::post('/droplet/{id}', [DashboardController::class, 'destroyDroplet']);
    Route::get('/droplets', [DashboardController::class, 'droplets']);
    Route::get('/application', [WebsiteController::class, 'showDomains']);
    Route::post('/application', [WebsiteController::class, 'addDomain']);
    Route::post('/application/{application}', [WebsiteController::class, 'removeDomain']);
    Route::get('/test', [DashboardController::class, 'test']);
});

Route::get('/{slug}/{id}', function ($slug, $id) {
    return [$slug, $id];
})->where('slug', '([0-9A-Za-z\-]+)')->where('id', '([0-9]+)');

// Route::any('/', [UserController::class, 'default']);
