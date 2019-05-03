<template>
    <v-container grid-list-md>
        <v-layout row>
            <v-flex xs12 v-if="configurations.length">
                <v-subheader>My configurations</v-subheader>
                <v-list>
                    <v-list-tile v-for="configuration in configurations" :key="configuration.id">
                        <v-list-tile-action>{{ configuration.id }}</v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title v-text="configuration.name"></v-list-tile-title>
                        </v-list-tile-content>
                        <v-list-tile-action>
                            <v-layout row>
                                <v-flex xs12>
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on }">
                                            <v-btn flat icon color="primary" v-on="on" @click="getConfiguration(configuration.id)">
                                                <v-icon>update</v-icon>
                                            </v-btn>
                                        </template>
                                        <span>Update</span>
                                    </v-tooltip>
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on }">
                                            <v-btn flat icon color="red" v-on="on" @click="deleteConfiguration(configuration.id)">
                                                <v-icon>delete</v-icon>
                                            </v-btn>
                                        </template>
                                        <span>Delete</span>
                                    </v-tooltip>
                                </v-flex>
                            </v-layout>
                        </v-list-tile-action>
                    </v-list-tile>
                </v-list>
            </v-flex>
        </v-layout>
        <create-configuration></create-configuration>
    </v-container>
</template>

<script>
    import CreateConfiguration from '../components/CreateConfiguration';
    import {EventBus} from "../event-bus";

    export default {
        data() {
            return {

            }
        },
        components: {
            CreateConfiguration,
        },
        methods: {
            getConfiguration(id) {
              this.$store.dispatch('GET_CONFIGURATION', id);
              EventBus.$emit('get-configuration');
            },
            deleteConfiguration(id) {
                this.$store.dispatch('REMOVE_CONFIGURATION', id);
            }
        },
        mounted() {

        },
        computed: {
            configurations() {
                return this.$store.getters.CONFIGURATIONS;
            },
            isLoggedIn() {
                return this.$store.getters.LOGGED;
            },
        }
    }
</script>
