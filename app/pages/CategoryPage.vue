<template>
    <div>
        <h1>Category</h1>

        <div v-for="product in products">
            <h2>{{ product.short_title }}</h2>
            <img :src="product.thumbnail" alt="">
            <ins>{{ product.regular_price }}</ins>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                products: []
            }
        },
        computed: {

        },
        methods: {

        },
        mounted() {
            console.log(this.$route.params.id);
            axios.post('/api/products', { id: this.$route.params.id })
                .then((response) => {
                    this.products = response.data;
                })
        }
    }
</script>

<style scoped>

</style>