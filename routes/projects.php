<?php

use App\Http\Controllers\ProjectsController;

Route::group(['middleware' => 'web', 'prefix' => 'projects'], function () {

    Route::get('movies', [
        'as' => 'projects',
        'uses' => ProjectsController::class.'@movies', /** see ProjectsController::movies() */
    ]);
});
