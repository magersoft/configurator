<template>
    <v-container>
        <h1>Categories</h1>

        <v-list>
            <v-list-tile
                    v-for="category in categories"
                    :key="category.title"
                    :to="{ name: 'category', params: { id: category.id } }"
            >
                <v-list-tile-action>
<!--                    <v-icon>{{ item.icon }}</v-icon>-->
                </v-list-tile-action>

                <v-list-tile-content>
                    <v-list-tile-title>{{ category.title }}</v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
    </v-container>
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