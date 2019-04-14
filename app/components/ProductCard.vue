<template>
    <div class="col-sm-6 col-md-4">
        <div class="thumbnail" style="height: 350px">
            <router-link :to="{ name: 'product', params: { id: product.id } }">
                <img :src="product.thumbnail" alt="..." style="height: 100px">
            </router-link>
            <div class="caption">
                <h4>{{ product.short_title }}</h4>
                <p style="font-size: 10px;">{{ product.short_description }}</p>
                <p><ins>{{ product.regular_price }}</ins></p>
                <p><del>{{ product.sale_price }}</del></p>
                <p>
                    <button
                            v-if="!forConfig"
                            :disabled="addedProduct(product)"
                            :class="{ 'btn-default': addedProduct(product), 'btn-primary': !addedProduct(product) }"
                            @click="addProduct(product, $event)"
                            class="btn"
                    >
                        {{ addedProduct(product) ? 'Added Product' : 'Add Product' }}
                    </button>
                    <button
                            v-if="forConfig"
                            @click="removeProduct(product)"
                            class="btn btn-danger"
                    >
                        Remove Product
                    </button>
                    <router-link tag="button" :to="{ name: 'product', params: { id: product.id } }" class="btn btn-default">More info</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            product: Object,
            forConfig: Boolean
        },
        data() {
            return {

            }
        },
        methods: {
            addProduct(product, event) {
                this.$store.dispatch('SAVE_PRODUCT', product);
                event.target.disabled = true;
                event.target.textContent = 'Added product';
            },
            addedProduct(product) {
                if (this.$store.getters.PRODUCTS) {
                    return this.$store.getters.PRODUCTS.map(item => item.id === product.id).filter(item => item === true)[0];
                }
            },
            removeProduct(product) {
                this.$store.dispatch('REMOVE_PRODUCT', product)
            }
        }
    }
</script>

<style scoped>

</style>