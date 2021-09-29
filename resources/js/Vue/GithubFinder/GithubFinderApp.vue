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
            >I'm Feeling Lucky!</button>
        </div><br>
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
            this.$http.get(this.routes.getUser, { params:  { user: this.userSearch }})
                .then((response) => {
                    if (response.data.status === 'success') {

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
