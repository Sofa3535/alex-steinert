<template>
    <div class="container">
        Search for a movie!

        <!--Search bar-->
        <div id="search-bar">
            <input
                id="search"
                type="text"
                placeholder="Try 'Avengers'"
                v-model="movieSearch"
            >
            <button
                id="search-btn"
                class="btn"
                @click="searchMovie"
            >Search!</button>
            <button
                id="lucky-btn"
                class="btn"
                @click="feelingLucky"
            >I'm Feeling Lucky!</button>
        </div><br>

        <!--Handle nothing in search box-->
        <div id="no-search" v-if="emptySearch">
            <p>It might help to type something in first...</p>
        </div>

        <!--Movie details and cast-->
        <div id="movie-result-success" v-if="this.status === 'success'">
            <div id="details">
                <h3 class="text-center">{{ this.details.title }}</h3>
                <img v-if="this.details.poster_path" class="rounded center" :src="'https://image.tmdb.org/t/p/w200' + this.details.poster_path"><br><br>
                <p>Synopsis: {{ this.details.overview ? this.details.overview : 'None' }}</p>
                <p>Released: {{ this.dateConversion ? this.dateConversion : 'Not Available' }}</p>
                <p v-if="this.details.runtime">Runtime: {{ this.details.runtime }} minutes ({{ this.timeConversion }})</p>
                <p v-else>Runtime not Available</p>
            </div>

            <div id="cast" v-if="cast && cast.length > 0">
                Note: An image of Java will be shown of anyone who doesn't have an uploaded photo
                <div class="person-container">
                    <div v-for="member in cast" class="person-item">
                        <img v-if="member.profile_path" class="rounded-circle square center" :src="'https://image.tmdb.org/t/p/w200' + member.profile_path">
                        <img v-else class="rounded-circle square center" src="https://alexsteinert.s3.ca-central-1.amazonaws.com/welcome/java-2.jpg"><br><br>
                        <p class="text-center">{{ member.character !== "" ? member.character : 'N/a' }}</p>
                        <p class="text-center">({{ member.name }})</p>
                    </div>
                </div>
            </div>
            <div v-else>There are no reported cast members</div>
        </div>

        <!--Handle no results-->
        <div id="no-results" v-else-if="this.status === 'no-results'">
            <p>No results found for  <strong>{{ this.movie }}</strong>. Try checking your spelling.</p>
        </div>

        <!--Handle unexpected errors-->
        <div id="error" v-else-if="this.status === 'error'">
            <p>There was an error. Try again or wait.</p>
        </div>


    </div>
</template>

<script>
import moment from 'moment'

export default {
    props: {
        routes: { required: true },
    },
    data() {
        return {
            movieSearch: '',
            emptySearch: false,
            movie: '',
            details: {},
            cast: {},
            status: '',
        }
    },
    methods: {
        searchMovie() {
            if (!this.movieSearch) {
                this.emptySearch = true;
                return;
            }
            this.emptySearch = false;
            $('.btn').prop('disabled', true);
            this.$http.get(this.routes.getMovies, { params:  { movie: this.movieSearch }})
                .then((response) => {
                    if (response.data.status === 'success') {
                        this.details = response.data.details
                        // Only show the first 10 cast members
                        this.cast = response.data.cast.cast.slice(0,10)
                    }
                    this.status = response.data.status
                })
                .catch(e =>  {
                    this.status = 'error';
                })
                .finally(() => {
                    $('.btn').prop('disabled', false);
                    this.movie = this.movieSearch
                })
        },
        feelingLucky() {
            this.emptySearch = false;
            $('.btn').prop('disabled', true);
            this.$http.get(this.routes.feelingLucky)
                .then((response) => {
                    if (response.data.status === 'success') {
                        this.details = response.data.details
                        // Only show the first 10 cast members
                        this.cast = response.data.cast.cast.slice(0,10)
                    }
                    this.status = response.data.status
                })
                .catch(e =>  {
                    this.status = 'error';
                })
                .finally(() => {
                    $('.btn').prop('disabled', false);
                    this.movie = this.movieSearch
                })
        }
    },
    computed: {
        // Turns boring date (09-24-2021) into fun date (Sept. 24, 2021)
        dateConversion() {
            let date = this.details.release_date
            return moment(date).format('MMM. D, YYYY')
        },
        // Turns minutes into hours & minutes
        timeConversion() {
            let time = this.details.runtime
            let hours = Math.trunc(time/60);
            let minutes = time % 60;

            // Putting it all together and checking for plurals
            return hours + ' hr' + (hours === 1 ? ' ' : 's ') + minutes + ' minute' + (minutes === 1 ? '' : 's');
        },
    }
}
</script>

<style>

.person-container {
    display: flex;
    flex-flow: row wrap;
    flex-direction: row;
}

.person-item {
    width: 33%;
    height: 33%;
    padding: 5%;
}

.square {
    object-fit: cover;
    width:230px;
    height:230px;
}

.center {
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.btn {
    background-color: #6405eb;
    color: white;
}

</style>
