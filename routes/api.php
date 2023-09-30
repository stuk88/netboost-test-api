<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrawlerController;

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

Route::get('/crawl', [CrawlerController::class, 'crawl']);
Route::get('/session_id', [CrawlerController::class, 'get_session_id']);
