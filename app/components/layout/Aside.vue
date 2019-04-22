<template>
    <v-list dense>
        <v-list-tile v-for="item in items" :key="item.title" :to="item.path">
            <v-list-tile-action>
                <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
                <v-list-tile-title>
                    {{ item.title }}
                </v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>
        <v-subheader class="mt-3 grey--text text--darken-1">Yii2 Framework</v-subheader>
        <v-list-tile href="/parser/citilink/index" class="mt-3" @click="">
            <v-list-tile-action>
                <v-icon color="grey darken-1">add_circle_outline</v-icon>
            </v-list-tile-action>
            <v-list-tile-title class="grey--text text--darken-1">Parser</v-list-tile-title>
        </v-list-tile>
        <v-list-tile @click="">
            <v-list-tile-action>
                <v-icon color="grey darken-1">settings</v-icon>
            </v-list-tile-action>
            <v-list-tile-title class="grey--text text--darken-1">Manage Subscriptions</v-list-tile-title>
        </v-list-tile>
    </v-list>
</template>

<script>
    export default {
        data() {
            return {
                drawer: true,
                items: [
                    { path: '/', title: 'Home', icon: 'dashboard' },
                    { path: '/about', title: 'About', icon: 'info' },
                    { path: '/login', title: 'Login', icon: 'person' },
                    { path: '/categories', title: 'Categories', icon: 'filter_list' },
                ]
            }
        }
    }
</script>

<style scoped>

</style>