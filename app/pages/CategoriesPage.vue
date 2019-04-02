<template>
    <div class="container">
        <h1>Categories</h1>
        <div v-for="category in categories">
            <router-link :to="{ name: 'category', params: { id: category.id } }">{{ category.title }}</router-link>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                categories: [],
            }
        },
        computed: {

        },
        methods: {
        },
        mounted() {
            axios('/api/categories')
                .then((response) => {
                    this.categories = response.data;
                });
        }
    }
</script>

<style scoped>

</style>