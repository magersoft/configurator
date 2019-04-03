<template>
    <div class="container">
        <h1>Category</h1>
        <div v-if="this.$route.params.id == 67">
            <button @click="getAMD">AMD</button>
            <button @click="getIntel">INTEL</button>
        </div>
        <div v-if="products.length === 0">Loading ...</div>
        <div v-infinite-scroll="loadMore" infinite-scroll-disabled="busy" infinite-scroll-distance="10">
            <div v-for="product in products">
                <router-link :to="{ name: 'product', params: { id: product.id } }">
                    <h2>{{ product.short_title }}</h2>
                    <img :src="product.thumbnail" alt="">
                </router-link>
                <ins>{{ product.regular_price }}</ins>
                <del>{{ product.sale_price }}</del>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                busy: false,
                products: [],
                nextPage: `/api/products?category_id=${this.$route.params.id}`
            }
        },
        methods: {
            loadMore() {
                if (!this.busy && this.nextPage) {
                    this.busy = true;

                    axios.get(this.nextPage)
                        .then(response => {
                            this.products.push(...response.data.result);
                            this.nextPage = response.data.pagination.next || null;
                            this.busy = false;
                        });
                }
            },
            getAMD() {
                axios.get('/api/products', {
                    params: { brand: 'AMD' }
                }).then(response => {
                    this.products = response.data.result;
                    this.nextPage = response.data.pagination.next || null;
                    this.busy = false;
                })
            },
            getIntel() {
                axios.get('/api/products', {
                    params: { brand: 'INTEL' }
                }).then(response => {
                    this.products = response.data.result;
                    this.nextPage = response.data.pagination.next || null;
                    this.busy = false;
                })
            }
        },
    }
</script>

<style scoped>

</style>