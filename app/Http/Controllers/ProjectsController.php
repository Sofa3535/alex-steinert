<?php

namespace App\Http\Controllers;

use App\Gurus\ProjectsGuru;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;

class ProjectsController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $guru;

    public function __construct() {
        $this->guru = new ProjectsGuru();
    }

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

    public function gitHub()
    {
        $routes = [
            'getUser' => route('projects.github.getUserApi', [], false),
        ];

        // Check to see if an access_token is stored in the session
        $accessToken = session('my_access_token');

        if ($accessToken) {
            $accessToken = true;
        } else {
            $accessToken = false;
        }

        return view('projects.github',[
            'routes' => $routes,
            'accessToken' => $accessToken,
            'clientId' => env('GITHUB_CLIENT_ID')
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

        [$movieDetails, $castSearchResp] = $this->guru->getMovieDetailsApi($movieSearchResp['id']);

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
            [$movieDetails, $castSearchResp] = $this->guru->getMovieDetailsApi($randomInt);
        } while ((isset($movieDetails['success']) && !$movieDetails['success']) || $movieDetails['adult']);

        return \response()->json([
            'status' => 'success',
            'details' => $movieDetails,
            'cast' => $castSearchResp
        ]);
    }

    public function loginGithub()
    {
        $code = \Request::get('code');
        $postParams = ['client_id' => env('GITHUB_CLIENT_ID'), 'client_secret' => env('GITHUB_CLIENT_SECRET'), 'code' => $code];
        $URL = 'https://github.com/login/oauth/access_token';

        // Doing this the laravel-y way seems to not like the github code we pass in, so let's do some good ol' fashioned PHP
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Acccept: application/json'));
        $accessToken = curl_exec($ch);
        curl_close($ch);

        //\Session::set('my_access_token', $accessToken);
        session(['my_access_token' => $accessToken]);

        return redirect(route('projects.github'));
    }

    public function getUserApi()
    {
        // Grab input data and start redis. User is lowercase so it's only cached once.
        $redis = Redis::connection();
        $user = strtolower(\Request::get('user'));
        $forkFilter = \Request::get('forked') === 'true' ? true : false;
        $accessToken = explode('=', explode('&',session('my_access_token'))[0])[1];

        // Check to see if the user is cached. If not, use the endpoints and then cache
        $redisRepos = $redis->get($user);
        $redisLanguages = $redis->get($user . '-languages');
        if ($redisRepos) {
            $userData = json_decode($redisRepos, true);
            $userAccount = $userData['user'];
            $repos = $userData['repos'];
        } else {
            $userAccount = \Http::withHeaders(['Authorization' => 'token ' . $accessToken])
                ->get('https://api.github.com/users/' . $user)
                ->json();
            // Get the all of the user's public repos, and private repos if they've authenticated
            $repos = \Http::withHeaders(['Authorization' => 'token ' . $accessToken])
                ->get('https://api.github.com/users/' . $user .'/repos')
                ->json();
            $userData['user'] = $userAccount;
            $userData['repos'] = $repos;
            $redis->set($user, json_encode($userData));
        }

        // Return early if the user is not found
        if (isset($repos['message']) && $repos['message'] === 'Not Found') {
            return \response()->json([
                'status' => 'no-results',
            ]);
        }

        // Initialize
        $totalRepoCount = 0;
        $stargazerCount = 0;
        $forkCount = 0;
        $languagesApi = [];
        $displayRepos = [];

        // Size is returned in KB
        $avgRepoSize = 0;

        // Iterate through repo to grab required data
        foreach ($repos as $repo) {
            // Only pull in data if the forked filter is true or if the repo has not been forked
            if ($forkFilter || !$repo['fork']) {
                $totalRepoCount++;
                $stargazerCount += $repo['stargazers_count'];
                $forkCount += $repo['forks'];
                $avgRepoSize += $repo['size'];
                $displayRepos[] = $repo;
            }
            // Grab the language url as the key and the forked attribute as a value so we can
            // cache all languages and filter depending on if the forked parameter is set
            $languagesApi[$repo['languages_url']] = $repo['fork'];
        }

        // Similarly to the beginning, if we are cached, grab, otherwise use endpoint then cache
        if ($redisLanguages) {
            $returnedRepo = json_decode($redisLanguages, true);
        } else {
            // Now we have to use an API to get each individual repo's languages
            // GraphQL would be SO much more efficient here, but it doesn't return counts
            $returnedRepo = [];
            foreach ($languagesApi as $api => $forked) {
                $returnedRepo[$api] = \Http::withHeaders(['Authorization' => 'token ' . $accessToken])
                    ->get($api)
                    ->json();
            }
            $redis->set($user . '-languages', json_encode($returnedRepo));
        }

        $languages = $this->guru->mergeGithubLanguages($returnedRepo, $languagesApi, $forkFilter);
        $avgRepoSize = $avgRepoSize / $totalRepoCount;

        // Return with all required data
         return \response()->json([
            'status' => 'success',
            'totalRepoCount' => $totalRepoCount,
            'stargazerCount' => $stargazerCount,
            'forkCount' => $forkCount,
            'avgRepoSize' => $avgRepoSize,
            'repos' => $displayRepos,
            'userDetails' => $userAccount,
            'languages' => $languages
        ]);
    }
}
