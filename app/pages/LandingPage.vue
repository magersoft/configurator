<template>
    <v-container grid-list-md text-xs-center>
        <v-layout row wrap>
            <v-flex xs12 sm6 md4 lg3 v-for="(product, key) of products">
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

            }
        },
        components: {
            ProductCard
        },
        methods: {

        },
        mounted() {
            this.$store.dispatch('GET_PRODUCTS');
        },
        computed: {
            products() {
                return this.$store.getters.PRODUCTS;
            }
        }
    }
</script>
