<template>
    <div class="container">
        <h2>{{ product.title }} <br><small>{{ product.short_title }}</small></h2>
        <p>{{ product.thumbnail }}</p>
        <p>{{ product.short_description }}</p>
        <p>Brand: {{ product.brand }}</p>
        <p>Category: {{ product.category }}</p>
        <div>
            <h3>Prices:</h3>
            <div v-for="(price, store) of product.pricesFormatted">
                <h4>{{ store }}</h4>
                <ul>
                    <li>Regular: {{ price.regular_price }}</li>
                    <li>Sale: {{ price.sale_price }}</li>
                    <li>Club: {{ price.club_price }}</li>
                </ul>
            </div>
        </div>
        <hr>
        <h3>Properties</h3>
        <div v-for="(property, group) of product.properties">
            <h4>{{ group }}</h4>
            <ul v-for="(value, item) of property">
               <li>{{ item }}: <strong>{{ value }}</strong></li>
            </ul>
        </div>
        <hr>
        <h3>Stocks</h3>
        <ul v-for="(count, name) of product.stocks">
           <li>{{ name }} - {{ count }}</li>
        </ul>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                product: {},
            }
        },
        mounted() {
            axios.post('/api/product', { id: this.$route.params.id })
                .then((response) => {
                    this.product = response.data.product;
                    console.log(response.data)
                })
        }
    }
</script>

<style scoped>

</style>