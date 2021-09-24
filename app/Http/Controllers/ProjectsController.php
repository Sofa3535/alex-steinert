<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;

class ProjectsController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('projects');
    }

    public function movies()
    {
        $routes = [
            'getMovies' => route('projects.movies.getMoviesApi', [], false)
        ];

        return view('projects.movies',[
            'routes' => $routes
        ]);

    }

    public function getMoviesApi()
    {
        $movie = \Request::get('movie');

        // Call to search for the movie
        $movieSearchResp = \Http::get('https://api.themoviedb.org/3/search/movie?api_key='
            .env('MOVIEDB_API_KEY').'&language=en-US&query='.$movie.'&page=1&include_adult=false')
        ->json()['results'];

        if (empty($movieSearchResp)) {
            return \response()->json([
                'status' => 'no-results',
            ]);
        }

        // Only need to grab the first result
        $movieSearchResp = $movieSearchResp[0];

        // Call to grab movie details
        $movieDetails = \Http::get('https://api.themoviedb.org/3/movie/'.$movieSearchResp['id'].'?api_key='
            .env('MOVIEDB_API_KEY').'&language=en-US')
            ->json();

        // Call to grab cast & crew
        $castSearchResp = \Http::get('https://api.themoviedb.org/3/movie/'.$movieSearchResp['id'].'/credits?api_key='
                .env('MOVIEDB_API_KEY').'&language=en-US')
            ->json();


        return \response()->json([
            'status' => 'success',
            'movie' => $movieSearchResp,
            'details' => $movieDetails,
            'cast' => $castSearchResp
        ]);
    }
}
