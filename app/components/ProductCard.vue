<template>
    <v-card class="product-card">
        <router-link :to="{ name: 'product', params: { id: product.id } }">
            <v-img :src="product.thumbnail" :lazy-src="product.thumbnail" aspect-ratio="1"></v-img>
        </router-link>

        <v-card-title primary-title>
            <h3 class="product-card_title">{{ product.short_title }}</h3>
            <div class="product-card_description">
                {{ product.short_description }}
            </div>
            <div class="product-card_price">
                <ins>{{ product.regular_price }}</ins>
                <del v-if="product.sale_price > 0">{{ product.sale_price }}</del>
            </div>
        </v-card-title>
        <v-card-actions>
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
        </v-card-actions>
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

<style lang="scss" scoped>
.product-card {
    &_title {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    &_description {
        height: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
        margin: 10px 0;
        font-size: 12px;
    }
    &_price {
        ins {
            font-size: 22px;
            text-decoration: none;
        }
    }
}
</style>