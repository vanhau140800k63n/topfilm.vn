<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\StorageController;

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

Route::prefix('movies')->name('movie.')->group(function () {
    Route::get('/id={id}&type={category}&name={name}', [MovieController::class, 'getMovie'])->name('detail'); // used
    Route::post('/episode-ajax', [MovieController::class, 'getEpisodeAjax'])->name('episode-ajax');
    Route::post('/get-view-movie-ajax', [MovieController::class, 'getViewMovieAjax'])->name('get-view-movie-ajax');
});

Route::get('/storage-ajax', [StorageController::class, 'saveImage'])->name('storage-ajax'); // used
Route::get('/storage-movie-ajax', [StorageController::class, 'saveMovie'])->name('storage-movie-ajax');
Route::get('/load_first_home_ajax', [HomeController::class, 'getFirstHomeAjax'])->name('load_first_home_ajax');

Route::get('/', [HomeController::class, 'getHomePage'])->name('home');
Route::get('/page={page}.{id}', [HomeController::class, 'searchMoreMovie'])->name('moremovie');
Route::get('/search={key}', [HomeController::class, 'searchMovie'])->name('search');
Route::get('/search_by_keyword_ajax/{key}', [HomeController::class, 'searchByKeywordAjax'])->name('search_by_keyword_ajax');
Route::get('/category/{id}', [HomeController::class, 'searchMovieCategory'])->name('category');
Route::get('/search/{value}', [HomeController::class, 'searchMovieAdvanced'])->name('search_advanced');
Route::post('/search_advanced_first', [HomeController::class, 'searchMovieAdvancedFirst'])->name('search_advanced_first');
Route::post('/search_advanced_more', [HomeController::class, 'searchMovieAdvancedMore'])->name('search_advanced_more');
Route::post('/key-search', [HomeController::class, 'searchKey'])->name('key-search');
Route::post('/home-ajax', [HomeController::class, 'getHomeAjax'])->name('home-ajax');
Route::get('/phim-{name}', [MovieController::class, 'getMovieByName'])->name('detail_name');
Route::post('/phim-{name}/update', [MovieController::class, 'postMovieUpdate'])->name('update');
Route::get('/phim-{name}/update', [MovieController::class, 'getMovieUpdate'])->name('update');
Route::get('/phim-{name}/tap-{episode_id}', [MovieController::class, 'getMovieByNameEposode'])->name('detail_name_episode');
