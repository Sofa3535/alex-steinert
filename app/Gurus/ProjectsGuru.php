<?php

namespace App\Gurus;


class ProjectsGuru
{
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
