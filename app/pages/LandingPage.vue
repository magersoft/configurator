<template>
    <div class="container">
        <!--<canvas id="c"></canvas>-->
        <div v-for="(product, key) of products">
            <product-card :product="product" for-config></product-card>
        </div>
    </div>
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
