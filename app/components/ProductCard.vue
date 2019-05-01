<template>
    <v-card class="product-card">
        <router-link :to="{ name: 'product', params: { id: product.id } }">
            <v-img :src="product.thumbnail" :lazy-src="product.thumbnail" aspect-ratio="1"></v-img>
        </router-link>

        <v-card-title primary-title>
            <h3 class="product-card_title">{{ product.short_title }}</h3>
            <div v-if="product.short_description" class="product-card_description">
                {{ product.short_description }}
            </div>
            <div v-if="product.prices" v-for="(price, store) of product.prices" class="product-card_price">
                <span>{{ store }}: </span>
                <ins>{{ price.regular_price }}</ins>
                <del v-if="price.sale_price > 0">{{ price.sale_price }}</del>
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
                    v-if="forConfig && !addedProduct(product)"
                    @click="selectConfigCategory"
                    color="primary"
            >
                Add
            </v-btn>
            <v-btn
                    v-if="forConfig && addedProduct(product)"
                    @click="removeProduct(product)"
                    color="error"
            >
                Remove
            </v-btn>
            <v-btn v-if="!forConfig" color="info" flat :to="{ name: 'product', params: { id: product.id } }">More info</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
    import { EventBus } from "../event-bus";

    export default {
        props: {
            product: Object,
            forConfig: Boolean
        },
        data() {
            return {
                category_id: this.product.id
            }
        },
        methods: {
            selectConfigCategory() {
                EventBus.$emit('select-config-category', this.category_id);
            },
            addProduct(product, event) {
                this.$store.dispatch('SAVE_PRODUCT', product);
                event.target.disabled = true;
                event.target.textContent = 'Added product';
                EventBus.$emit('close-dialog');
            },
            addedProduct(product) {
                if (this.$store.getters.PRODUCTS) {
                    return this.$store.getters.PRODUCTS.map(item => item.prices && item.id === product.id).filter(item => item === true)[0];
                }
            },
            removeProduct(product) {
                this.$store.dispatch('REMOVE_PRODUCT', product);
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