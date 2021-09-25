<?php

use App\Http\Controllers\ProjectsController;

Route::group(['middleware' => 'web', 'prefix' => 'projects'], function () {

    Route::get('movies', [
        'as' => 'projects.movies',
        'uses' => ProjectsController::class.'@movies', /** see ProjectsController::movies() */
    ]);

    Route::get('movies/getMoviesApi', [
        'as' => 'projects.movies.getMoviesApi',
        'uses' => ProjectsController::class.'@getMoviesApi', /** see ProjectsController::getMoviesApi() */
    ]);

    Route::get('movies/getRandomApi', [
        'as' => 'projects.movies.getRandomApi',
        'uses' => ProjectsController::class.'@feelingLuckyApi', /** see ProjectsController::feelingLuckyApi() */
    ]);
});
