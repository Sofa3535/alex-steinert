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
            'getMovies' => route('projects.movies.getMoviesApi', [], false),
            'feelingLucky' => route('projects.movies.getRandomApi', [], false)
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

        [$movieDetails, $castSearchResp] = $this->getMovieDetailsApi($movieSearchResp['id']);

        return \response()->json([
            'status' => 'success',
            'details' => $movieDetails,
            'cast' => $castSearchResp
        ]);
    }

    public function feelingLuckyApi()
    {
        // Get the latest movie inserted so I can tell how far up the ids go
        $latestMovie = \Http::get('https://api.themoviedb.org/3/movie/latest?api_key='.env('MOVIEDB_API_KEY').'&language=en-US')
            ->json();

        // Grab the id
        $latestId = $latestMovie['id'];


        // Then find a random int (which will be our movie id). If the movie returns a 200 or is an adult movie,
        // we need to try again with a new int
        do {
            $randomInt = rand(1, $latestId);
            [$movieDetails, $castSearchResp] = $this->getMovieDetailsApi($randomInt);
        } while ((isset($movieDetails['success']) && !$movieDetails['success']) || $movieDetails['adult']);

        return \response()->json([
            'status' => 'success',
            'details' => $movieDetails,
            'cast' => $castSearchResp
        ]);
    }

    public function getMovieDetailsApi($movieId)
    {
        // Call to grab movie details
        $movieDetails = \Http::get('https://api.themoviedb.org/3/movie/'.$movieId.'?api_key='
            .env('MOVIEDB_API_KEY').'&language=en-US')
            ->json();

        // Call to grab cast & crew
        $castSearchResp = \Http::get('https://api.themoviedb.org/3/movie/'.$movieId.'/credits?api_key='
            .env('MOVIEDB_API_KEY').'&language=en-US')
            ->json();

        return [$movieDetails, $castSearchResp];
    }
}
