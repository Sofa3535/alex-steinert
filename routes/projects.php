<?php

use App\Http\Controllers\ProjectsController;

Route::group(['middleware' => 'web', 'prefix' => 'projects'], function () {

    Route::get('movies', [
        'as' => 'projects.movies',
        'uses' => ProjectsController::class.'@movies', /** see ProjectsController::movies() */
    ]);

    Route::get('github', [
        'as' => 'projects.github',
        'uses' => ProjectsController::class.'@github', /** see ProjectsController::github() */
    ]);

    Route::get('movies/getMoviesApi', [
        'as' => 'projects.movies.getMoviesApi',
        'uses' => ProjectsController::class.'@getMoviesApi', /** see ProjectsController::getMoviesApi() */
    ]);

    Route::get('movies/getRandomApi', [
        'as' => 'projects.movies.getRandomApi',
        'uses' => ProjectsController::class.'@feelingLuckyApi', /** see ProjectsController::feelingLuckyApi() */
    ]);

    Route::get('github/login', [
        'as' => 'projects.github.login',
        'uses' => ProjectsController::class.'@loginGithub', /** see ProjectsController::loginGithub() */
    ]);

    Route::get('github/getUserApi', [
        'as' => 'projects.github.getUserApi',
        'uses' => ProjectsController::class.'@getUserApi', /** see ProjectsController::getUserApi() */
    ]);


    Route::get('github/getRandomApi', [
        'as' => 'projects.github.getRandomApi',
        'uses' => ProjectsController::class.'@githubFeelingLuckyApi', /** see ProjectsController::githubFeelingLuckyApi() */
    ]);
});
