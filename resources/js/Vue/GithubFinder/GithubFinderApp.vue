<template>
    <div class="container">
        Search for a GitHub User!

        <!--Search bar-->
        <div id="search-bar">
            <input
                id="search"
                type="text"
                v-model="userSearch"
            >
            <button
                id="search-btn"
                class="btn"
                @click="searchUser"
            >Search!</button>
            <button
                id="lucky-btn"
                class="btn"
                @click="feelingLucky"
            >I'm Feeling Lucky!</button><br>
            <input type="checkbox" id="forked" v-model="forked">
            <label for="forked">Show Forked Repos</label>
        </div>

        <div class="metrics">
            <p>Total # of Repos: {{ this.totalRepoCount }}</p>
            <p>Total # of Stargazers: {{ this.stargazerCount }}</p>
            <p>Total # of Forks: {{ this.forkCount }}</p>
            <p>Average Repo size: {{ this.avgRepoSize }} KB</p>
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
            userSearch: '',
            emptySearch: false,
            status: '',
            user: '',
            forked: true,
            totalRepoCount: 0,
            stargazerCount: 0,
            forkCount: 0,
            avgRepoSize: 0
        }
    },
    methods: {
        searchUser() {
            if (!this.userSearch) {
                this.userSearch = true;
                return;
            }
            this.emptySearch = false;
            $('.btn').prop('disabled', true);
            this.$http.get(this.routes.getUser, { params:  { user: this.userSearch, forked: this.forked }})
                .then((response) => {
                    if (response.data.status === 'success') {
                        this.totalRepoCount = response.data.totalRepoCount
                        this.stargazerCount = response.data.stargazerCount
                        this.forkCount = response.data.forkCount
                        this.avgRepoSize = response.data.avgRepoSize
                    }
                    this.status = response.data.status
                })
                .catch(e =>  {
                    this.status = 'error';
                })
                .finally(() => {
                    $('.btn').prop('disabled', false);
                    this.user = this.userSearch
                })
        },
        feelingLucky() {

        }
    },
    computed: {

    }
}
</script>

<style>

</style>
