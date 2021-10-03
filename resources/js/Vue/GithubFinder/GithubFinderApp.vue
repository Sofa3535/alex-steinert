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
                >Search!</button><br>
                <input type="checkbox" id="forked" v-model="forked">
                <label for="forked">Show Forked Repos</label>
            </div>

            <!--Handle nothing in search box-->
            <div id="no-search" v-if="emptySearch">
                <p class="alert alert-danger">Cannot leave search empty!</p>
            </div>

            <!--Handle unexpected errors-->
            <div id="error" v-else-if="this.status === 'error'">
                <p class="alert alert-danger">There was an error. Try again or wait.</p>
            </div>

            <!--Handle no results-->
            <div id="no-results" v-else-if="this.status === 'no-results'">
                <p class="alert alert-invo">No results found for  <strong>{{ this.user }}</strong>. Try checking your spelling.</p>
            </div>

            <div class="metrics" v-if="this.status === 'success'">
                <div id="user-details" class="container" style="text-align: center">
                    <h4>{{ this.userDetails.name ? this.userDetails.name : this.user }}</h4>
                    <img class="resize center" :src="this.userDetails.avatar_url">
                </div>
                <p>Total # of Repos: {{ this.totalRepoCount }}</p>
                <p>Total # of Stargazers: {{ this.stargazerCount }}</p>
                <p>Total # of Forks: {{ this.forkCount }}</p>
                <p>Average Repo size: {{ this.formatSize }}</p>
                <div>Languages Used:
                    <ol>
                       <li v-for="(count, language) in languages">{{ language }} - {{ count.toLocaleString() }}</li>
                    </ol>
                </div>

                <div id="repo-card-container">
                    <div class="repo-card" v-for="repo in repos">
                        <a :href="repo.html_url" target="_blank"><h3>{{ repo.name }}</h3></a>
                        <p>{{ repo.description ? repo.description : 'No Description'}}</p>
                    </div>
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
            languages: [],
            userDetails: [],
            repos: [],
        }
    },
    methods: {
        searchUser() {
            if (!this.userSearch) {
                this.emptySearch = true;
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
                        this.userDetails = response.data.userDetails
                        this.repos = response.data.repos
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
        }
    },
    computed: {
        formatSize() {
            if (this.avgRepoSize <= 999) {
                return (Math.round(this.avgRepoSize * 100) / 100) + ' KB'
            } else if (this.avgRepoSize >= 1000 && this.avgRepoSize <= 999999) {
                return (Math.round((this.avgRepoSize/1000) * 100) / 100) + ' MB'
            } else {
                return (Math.round((this.avgRepoSize/1000000) * 100) / 100) + ' GB'
            }
        }
    }
}
</script>

<style>

    #repo-card-container {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .repo-card {
        width: 33%;
        padding: 1%;
    }

    .resize {
        width: 300px;
        height: auto;
        box-shadow: 10px 5px 5px #CCC;
        border-radius: 15px;
    }

    .center {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 300px;
    }

    #user-details {
        position: absolute;
    }

</style>
