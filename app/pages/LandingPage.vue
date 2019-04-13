<template>
    <div class="container">
        <!--<canvas id="c"></canvas>-->
        <div v-for="(product, key) of products">
            <router-link :to="{ name: 'product', params: { id: product.id } }">
                <h2>{{ product.short_title }}</h2>
                <img :src="product.thumbnail" alt="">
            </router-link>
            <ins>{{ product.regular_price }}</ins>
            <del>{{ product.sale_price }}</del>
            <br>
            <button @click="removeProduct(product)" :key="key" class="btn btn-danger">Remove product</button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {

            }
        },
        methods: {
            removeProduct(product) {
                this.$store.dispatch('REMOVE_PRODUCT', product)
            }
        },
        created() {
          console.log('created');
          console.log(this.$store.getters.PRODUCTS);
        },
        mounted() {
            this.$store.dispatch('GET_PRODUCTS');
            console.log('mounted');
            console.log(this.$store.state.products);
        },
        computed: {
            products() {
                console.log('computed');
                return this.$store.getters.PRODUCTS;
            }
        }
    }
</script>
