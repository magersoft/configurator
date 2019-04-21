<template>
    <v-container grid-list-md text-xs-center>
        <h1>Category</h1>
        <v-layout column v-if="this.$route.params.id == 67">
            <v-checkbox v-model="amd" @change="getAMD" label="AMD"></v-checkbox>
            <v-checkbox v-model="intel" @change="getIntel" label="Intel"></v-checkbox>
        </v-layout>
        <v-layout row>
            <v-text-field v-model="searchProduct" label="Название продукта"></v-text-field>
        </v-layout>
        <v-progress-circular
                v-if="!products.length"
                indeterminate
                color="primary"
        ></v-progress-circular>
        <div v-infinite-scroll="loadMore" infinite-scroll-disabled="busy" infinite-scroll-distance="10">
            <v-layout row wrap>
                <v-flex xs12 sm6 md4 lg3 v-for="(product, key) of searchedProduct" :key="key">
                    <product-card :product="product"></product-card>
                </v-flex>
            </v-layout>
        </div>
        <div v-if="!searchedProduct.length && products.length">Nothing not founds</div>
    </v-container>
</template>

<script>
    import ProductCard from '../components/ProductCard';
    export default {
        data() {
            return {
                busy: false,
                amd: this.$store.getters.AMD,
                intel: this.$store.getters.INTEL,
                searchProduct: [],
                products: [],
                nextPage: `/api/products?category_id=${this.$route.params.id}`
            }
        },
        components: {
          ProductCard
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
        mounted() {
            this.$store.dispatch('GET_PRODUCTS');
        },
        methods: {
            loadMore() {
                if (this.amd) {
                    this.getAMD();
                    return;
                }
                if (this.intel) {
                    this.getIntel();
                    return;
                }
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
                this.$store.commit('SET_AMD', this.amd);
                axios.get('/api/products', {
                    params: { category_id: this.$route.params.id, brand: this.amd ? 'AMD' : null }
                }).then(response => {
                    this.products = response.data.result;
                    this.nextPage = response.data.pagination.next || null;
                    this.busy = false;
                })
            },
            getIntel() {
                this.$store.commit('SET_INTEL', this.intel);
                axios.get('/api/products', {
                    params: { category_id: this.$route.params.id, brand: this.intel ? 'INTEL' : null }
                }).then(response => {
                    this.products = response.data.result;
                    this.nextPage = response.data.pagination.next || null;
                    this.busy = false;
                })
            },
        },
    }
</script>

<style scoped>

</style>