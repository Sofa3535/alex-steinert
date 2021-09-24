<template>
    <div class="container">
        Hello World! Search for a movie!
        <div id="search-bar">
            <input
                id="search"
                type="text"
                placeholder="Try 'Avengers'"
                v-model="movieSearch"
            >
            <button
                id="search-btn"
                @click="searchMovie"
            >Search!</button>
        </div>

        <div id="movie-result-success" v-if="this.status === 'success'">
            <div id="details">
                <h3>{{ this.movieResult.title }}</h3>
                <p>{{ this.movieResult.overview }}</p>
                <p v-if="this.movieResult.release_date">Released: {{ this.dateConversion }}</p>
                <p v-if="this.details.runtime">Runtime: {{ this.details.runtime }} minutes ({{ this.timeConversion }})</p>
            </div>

            <div id="cast" v-if="cast && cast.length > 0">
                <ul>
                    <li v-for="member in cast">{{ member.character }} ({{ member.name }})</li>
                </ul>
            </div>
            <div v-else>There are no cast members</div>
        </div>

        <div id="no-results" v-else-if="this.status === 'no-results'">
            <p>No results found for  <strong>{{ this.movie }}</strong>. Try checking your spelling.</p>
        </div>

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
            movie: '',
            movieResult: {},
            details: {},
            cast: {},
            status: '',
        }
    },
    methods: {
        searchMovie() {
            $('#search-btn').prop('disabled', true);
            this.$http.get(this.routes.getMovies, { params:  { movie: this.movieSearch }})
                .then((response) => {
                    if (response.data.status === 'success') {
                        this.movieResult = response.data.movie
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
                    $('#search-btn').prop('disabled', false);
                    this.movie = this.movieSearch
                })
        }
    },
    computed: {
        // Turns boring date (09-24-2021) into fun date (Sept. 24, 2021)
        dateConversion() {
            let date = this.movieResult.release_date
            return moment(date).format('MMM. D, YYYY')
        },
        // Turns minutes into hours & minutes
        timeConversion() {
            let time = this.details.runtime
            let hours = Math.trunc(time/60);
            let minutes = time % 60;
            return hours + ' hr' + (hours === 1 ? ' ' : 's ') + minutes + ' minute' + (minutes === 1 ? '' : 's');
        },
    }
}
</script>
