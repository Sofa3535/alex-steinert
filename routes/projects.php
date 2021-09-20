<?php

use App\Http\Controllers\ProjectsController;

Route::group(['middleware' => 'web', 'prefix' => 'projects'], function () {

    Route::get('/blackjack', [
        'as' => 'projects.blackjack',
        'uses' => ProjectsController::class.'@blackJack', /** see ProjectsController::blackJack() */
    ]);

});
