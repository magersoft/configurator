<template>
    <div>
        <h1>Products</h1>
        <div v-for="product in products">
            <h2>{{ product.title }}</h2>
            <img :src="product.thumbnail" alt="">
            <ins>{{ product.regular_price }}</ins>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                products: [],
            }
        },
        computed: {

        },
        methods: {

        },
        mounted() {
            axios({
                method: 'post',
                url: '/api/products'
            }).then((response) => {
                this.products = response.data;
            })
        }
    }
</script>

<style scoped>

</style>