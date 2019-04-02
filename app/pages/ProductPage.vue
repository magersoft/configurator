<template>
    <div class="container">
        <h2>{{ product.title }}</h2>
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