<template>
    <div class="container">
        <h1>Category</h1>
        <div v-if="this.$route.params.id == 67">
            <button @click="getAMD">AMD</button>
            <button @click="getIntel">INTEL</button>
        </div>
        <hr>
        <div class="row">
            <div class="form-group col-md-3">
                <label for="searchProduct" class="control-label">Название продукта</label>
                <input v-model="searchProduct" type="text" id="searchProduct" class="form-control" placeholder="Начните ввод ...">
            </div>
        </div>

        <div v-if="!products.length">Loading ...</div>
        <div v-infinite-scroll="loadMore" infinite-scroll-disabled="busy" infinite-scroll-distance="10">
            <div v-for="(product, key) of searchedProduct">
                <router-link :to="{ name: 'product', params: { id: product.id } }">
                    <h2>{{ product.short_title }}</h2>
                    <img :src="product.thumbnail" alt="">
                </router-link>
                <ins>{{ product.regular_price }}</ins>
                <del>{{ product.sale_price }}</del>
                <br>
                <button @click="addProduct(product, $event)" :key="key" class="btn btn-success" style="margin-top: 5px;">Add product</button>
            </div>
        </div>
        <div v-if="!searchedProduct.length">Nothing not founds</div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                busy: false,
                searchProduct: [],
                products: [],
                nextPage: `/api/products?category_id=${this.$route.params.id}`
            }
        },
        computed: {
            searchedProduct() {
                return this.products.filter((product) => {
                    if (product.short_title.toLowerCase().indexOf(this.searchProduct) !== -1) {
                        return product;
                    }
                })
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
                    params: { category_id: this.$route.params.id, brand: 'AMD' }
                }).then(response => {
                    this.products = response.data.result;
                    this.nextPage = response.data.pagination.next || null;
                    this.busy = false;
                })
            },
            getIntel() {
                axios.get('/api/products', {
                    params: { category_id: this.$route.params.id, brand: 'INTEL' }
                }).then(response => {
                    this.products = response.data.result;
                    this.nextPage = response.data.pagination.next || null;
                    this.busy = false;
                })
            },
            addProduct(product, event) {
                this.$store.dispatch('SAVE_PRODUCT', product);
                event.target.disabled = true;
                event.target.textContent = 'Added';
            }
        },
    }
</script>

<style scoped>

</style>