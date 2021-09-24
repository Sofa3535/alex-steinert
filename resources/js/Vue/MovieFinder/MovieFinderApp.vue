<template>
    <div class="container">
        Hello World! Search for a movie!
        <div id="search-bar">
            <input
                id="search"
                type="text"
                placeholder="Try 'Avengers: Endgame'"
                v-model="movieSearch"
            >
            <button
                @click="searchMovie"
            >Search!</button>
        </div>

        <div id="movie-result">
            <h3>{{ this.movieFound.title }}</h3>
            <p>{{ this.movieFound.overview }}</p>
            <p v-if="this.movieFound.release_date">Released: {{ this.dateConversion }}</p>
            <p v-if="this.details.runtime">Runtime: {{ this.details.runtime }} minutes ({{ this.timeConversion }})</p>
        </div>

        <div id="cast" v-if="cast">
            <ul>
                <li v-for="member in cast">{{ member.character }} ({{ member.name }})</li>
            </ul>
        </div>
    </div>
</template>

<script>
import moment from 'moment'

export default {
    props: {
        movieSearch: { required: false },
        routes: { required: true },
    },
    data() {
        return {
            movieFound: {},
            details: {},
            cast: {},
        }
    },
    methods: {
        searchMovie() {
            this.$http.get(this.routes.getMovies, { params:  { movie: this.movieSearch }})
                .then((response) => {
                    this.movieFound = response.data.movie
                    this.details = response.data.details
                    // Only show the first 10 cast members
                    this.cast = response.data.cast.cast.slice(0,10)
                })
                .catch()
                .finally(() => {
                })
        }
    },
    computed: {
        dateConversion() {
            let date = this.movieFound.release_date
            return moment(date).format('MMM. D, YYYY')
        },
        timeConversion() {
            let time = this.details.runtime
            let hours = Math.trunc(time/60);
            let minutes = time % 60;
            return hours +" hrs "+ minutes + ' minute' + (minutes === 1 ? '' : 's');
        },
    },
    mounted() {

    }
}
</script>
