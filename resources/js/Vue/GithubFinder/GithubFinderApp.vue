<template>
    <div class="container">

        <div id="github-auth" v-if="!accessToken">
            <p>You must authenticate with GitHub before you use this application</p>
            <a href="https://github.com/login/oauth/authorize?client_id=1ead0ff6b3ec2fe3924a" class="btn btn-primary">Authorize with GitHub</a>
        </div>

        <div id="github-search" v-else>
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
                <div>Languages Used:
                    <ol>
                       <li v-for="(count, language) in languages">{{ language }} - {{ count.toLocaleString() }}</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>
</template>

<script>

export default {
    props: {
        routes: { required: true },
        accessToken: { required: true },
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
            avgRepoSize: 0,
            languages: []
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
                        this.languages = response.data.languages
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
    }
}
</script>

<style>

</style>
