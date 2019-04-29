<template>
    <v-app :dark="dark">
        <v-navigation-drawer
                v-model="primaryDrawer.model"
                :clipped="primaryDrawer.clipped"
                :temporary="primaryDrawer.type === 'temporary'"
                absolute
                overflow
                app
        >
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
        </v-navigation-drawer>
        <v-toolbar color="primary" dark :clipped-left="primaryDrawer.clipped" app absolute>
            <v-toolbar-side-icon @click.stop="primaryDrawer.model = !primaryDrawer.model"></v-toolbar-side-icon>
            <v-toolbar-title>Configurator App</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-toolbar-items class="hidden-sm-and-down">
                <v-btn flat v-for="item in items" :key="item.title" :to="item.path"><v-icon left>{{ item.icon }}</v-icon> {{ item.title }}</v-btn>
            </v-toolbar-items>
        </v-toolbar>
        <v-content>
            <v-container fluid>
                <router-view></router-view>
            </v-container>
        </v-content>
        <v-footer app>
            <v-spacer></v-spacer>
            <v-switch
                    v-model="dark"
                    :label="`Dark: ${dark.toString()}`"
            ></v-switch>
            <span class="px-3">&copy; {{ new Date().getFullYear() }}</span>
        </v-footer>
    </v-app>
</template>

<script>
    export default {
        data() {
            return {
                dark: false,
                primaryDrawer: {
                    model: null,
                    type: 'temporary',
                    clipped: true,
                    floating: false,
                    mini: false
                },
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