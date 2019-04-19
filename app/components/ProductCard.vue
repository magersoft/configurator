<template>
    <v-card>
        <div class="thumbnail">
            <router-link :to="{ name: 'product', params: { id: product.id } }">
                <img :src="product.thumbnail" alt="..." style="height: 100px">
            </router-link>
            <div class="caption">
                <h4>{{ product.short_title }}</h4>
                <p style="font-size: 10px;">{{ product.short_description }}</p>
                <p><ins>{{ product.regular_price }}</ins></p>
                <p><del>{{ product.sale_price }}</del></p>
                <p>
                    <v-btn
                        v-if="!forConfig"
                        :disabled="addedProduct(product)"
                        :class="{ 'btn-default': addedProduct(product), 'btn-primary': !addedProduct(product) }"
                        @click="addProduct(product, $event)"
                        color="info"
                    >
                        {{ addedProduct(product) ? 'Added Product' : 'Add Product' }}
                    </v-btn>
                    <v-btn
                        v-if="forConfig"
                        @click="removeProduct(product)"
                        color="error"
                    >
                        Remove Product
                    </v-btn>
                    <v-btn color="info" flat :to="{ name: 'product', params: { id: product.id } }">More info</v-btn>
                </p>
            </div>
        </div>
    </v-card>
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