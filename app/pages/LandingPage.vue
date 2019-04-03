<template>
    <div class="container">
        <!--<canvas id="c"></canvas>-->
        <div v-for="product of products">
            {{ product }}
        </div>
        <button @click="addProduct">press</button>
    </div>
</template>

<script>
    export default {
        data() {
            return {

            }
        },
        methods: {
          addProduct() {
              let item = 'Testing Vuex';
              this.$store.dispatch('SAVE_PRODUCT', item);
          }
        },
        mounted() {
            console.log(this.$store);
            // console.log(this.$store.dispatch('GET_PRODUCT'));
        },
        computed: {
            products() {
                return this.$store.getters.PRODUCTS;
            }
        }
    }
</script>
