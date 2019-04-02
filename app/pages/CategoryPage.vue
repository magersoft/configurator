<template>
    <div class="container">
        <h1>Category</h1>
        <div v-if="this.$route.params.id == 67">
            <button @click="getAMD">AMD</button>
            <button @click="getIntel">INTEL</button>
        </div>
        <div v-for="product in products">
            <h2><router-link :to="{ name: 'product', params: { id: product.id } }">{{ product.short_title }}</router-link></h2>
            <!--<img :src="product.thumbnail" alt="">-->
            <ins>{{ product.regular_price }}</ins>
            <del>{{ product.sale_price }}</del>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'CategoryPage',
        data() {
            return {
                products: []
            }
        },
        computed: {

        },
        methods: {
            getAMD() {
                axios.post('/api/products', { brand: 'AMD' })
                    .then((response) => {
                        this.products = response.data;
                    })
            },
            getIntel() {
                axios.post('/api/products', { brand: 'INTEL' })
                    .then((response) => {
                        this.products = response.data;
                    })
            }
        },
        mounted() {
            console.log(this.$route.params.id);
            axios.post('/api/products', { category_id: this.$route.params.id })
                .then((response) => {
                    this.products = response.data;
                })
        }
    }
</script>

<style scoped>

</style>