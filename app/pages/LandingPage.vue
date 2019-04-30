<template>
    <v-container grid-list-md text-xs-center>
        <v-layout row v-if="isLoggedIn">
            <v-flex xs12>
                <v-list>
                    <v-list-tile v-for="configuration in configurations" :key="configuration.id">
                        <v-list-tile-action>{{ configuration.id }}</v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{ configuration.token }}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
                <v-btn color="success">Create configuration</v-btn>
            </v-flex>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12 sm6 md4 lg3 v-for="(product, key) of products" :key="key">
                <product-card :product="product" for-config></product-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import ProductCard from '../components/ProductCard';
    export default {
        data() {
            return {
                configurations: [],
            }
        },
        components: {
            ProductCard
        },
        methods: {

        },
        mounted() {
            axios.get('/api/configurations')
                .then(response => {
                    const { data } = response;
                    this.configurations = data.configurations;
                })
        },
        computed: {
            products() {
                return this.$store.getters.PRODUCTS;
            },
            isLoggedIn() {
                return this.$store.getters.LOGGED;
            },
        }
    }
</script>
