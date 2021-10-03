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

    /**
     * @param array $returnedRepo
     * @param $languagesApi
     * @param $forkFilter
     * @return array
     *
     * Merges and sorts all languages together and filters based on the forked flag
     */
    public function mergeGithubLanguages(array $returnedRepo, $languagesApi, $forkFilter)
    {
        $languages = [];
        foreach ($returnedRepo as $api => $repoLanguages) {
            if ($forkFilter || !$languagesApi[$api]) {
                foreach ($repoLanguages as $language => $count) {
                    isset($languages[$language]) ? $languages[$language] += $count : $languages[$language] = $count;
                }
            }
        }

        arsort($languages);
        return $languages;
    }
}
