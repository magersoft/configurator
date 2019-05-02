<template>
    <v-layout row text-xs-center>
        <v-flex xs12>
            <v-btn @click="getConfigurationItems" color="success">Create configuration</v-btn>
        </v-flex>
        <v-dialog
                v-model="dialog"
                fullscreen
                hide-overlay
                transition="dialog-bottom-transition"
                scrollable
        >
            <v-card tile>
                <v-toolbar card dark color="primary">
                    <v-btn icon dark @click="dialog = false">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Create configuration</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click="saveConfiguration">Save</v-btn>
                    </v-toolbar-items>
                    <v-menu bottom right offset-y>
                        <template v-slot:activator="{ on }">
                            <v-btn dark icon v-on="on">
                                <v-icon>more_vert</v-icon>
                            </v-btn>
                        </template>
                        <v-list>
                            <v-list-tile v-for="(item, i) in items" :key="i" @click="">
                                <v-list-tile-title>{{ item.title }}</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </v-toolbar>
                <v-card-text>
                    <v-form>
                        <v-container fluid>
                            <v-layout>
                                <v-flex xs12 md6>
                                    <v-text-field
                                            v-model="name"
                                            label="Configuration name"
                                    ></v-text-field>
                                </v-flex>
                                <v-flex xs12 md6>
                                    <v-select
                                            :items="type"
                                            label="Type"
                                    ></v-select>
                                </v-flex>
                            </v-layout>
                            <v-layout row wrap justify-center>
                                <v-progress-circular
                                        v-if="!configurationItems"
                                        indeterminate
                                        color="primary"
                                ></v-progress-circular>
                                <v-flex xs12 sm6 md4 lg2 v-for="item of configurationItems" :key="item.id">
                                    <product-card :product="item" for-config></product-card>
                                </v-flex>
                            </v-layout>
                            <v-divider></v-divider>
                            <v-list three-line subheader>
                                <v-subheader>General</v-subheader>
                                <v-list-tile avatar>
                                    <v-list-tile-action>
                                        <v-checkbox v-model="notifications"></v-checkbox>
                                    </v-list-tile-action>
                                    <v-list-tile-content>
                                        <v-list-tile-title>Notifications</v-list-tile-title>
                                        <v-list-tile-sub-title>Notify me about updates to apps or games that I downloaded</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-list-tile avatar>
                                    <v-list-tile-action>
                                        <v-checkbox v-model="sound"></v-checkbox>
                                    </v-list-tile-action>
                                    <v-list-tile-content>
                                        <v-list-tile-title>Sound</v-list-tile-title>
                                        <v-list-tile-sub-title>Auto-update apps at any time. Data charges may apply</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-list-tile avatar>
                                    <v-list-tile-action>
                                        <v-checkbox v-model="widgets"></v-checkbox>
                                    </v-list-tile-action>
                                    <v-list-tile-content>
                                        <v-list-tile-title>Auto-add widgets</v-list-tile-title>
                                        <v-list-tile-sub-title>Automatically add home screen widgets</v-list-tile-sub-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </v-list>
                        </v-container>
                    </v-form>
                </v-card-text>

                <div style="flex: 1 1 auto;"></div>
            </v-card>
        </v-dialog>
        <v-dialog
                v-model="dialog2"
                fullscreen
                hide-overlay
                transition="dialog-bottom-transition"
                scrollable
        >
            <v-card tile>
                <v-toolbar card dark color="primary">
                    <v-btn icon dark @click="dialog2 = false">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Cat name</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <v-container grid-list-md text-xs-center>
                        <v-layout row wrap justify-center>
                            <v-progress-circular
                                    v-if="!products.length"
                                    indeterminate
                                    color="primary"
                            ></v-progress-circular>
                            <v-flex xs12 sm6 md4 lg3 v-for="(product, key) of products" :key="key">
                                <product-card :product="product"></product-card>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>

                <div style="flex: 1 1 auto;"></div>
            </v-card>
        </v-dialog>
    </v-layout>
</template>

<script>
    import { EventBus } from "../event-bus";
    import productCard from './ProductCard';

    export default {
        components: {
            productCard
        },
        data() {
            return {
                dialog: false,
                dialog2: false,
                notifications: false,
                sound: true,
                widgets: false,
                name: `New configuration`,
                type: ['Home', 'Gaming', 'Office'],
                products: [],
                items: [
                    {
                        title: 'Click Me'
                    },
                    {
                        title: 'Click Me'
                    },
                    {
                        title: 'Click Me'
                    },
                    {
                        title: 'Click Me 2'
                    }
                ],
            }
        },
        mounted() {
            if (this.dialog) {
                this.getConfigurationItems();
            }
            EventBus.$on('select-config-category', id => {
                axios.get('/api/products', { params: { category_id: id } })
                    .then(response => {
                        this.dialog2 = true;
                        this.products = response.data.result;
                    })
            });
            EventBus.$on('close-dialog', () => {
                this.dialog2 = false
            });
        },
        methods: {
            getConfigurationItems() {
                this.dialog = true;
                this.$store.dispatch('CREATE_CONFIGURATION');
            },
            saveConfiguration() {
                this.dialog = false;
                const payload = {
                    id: this.$store.getters.CURRENT_CONFIGURATION.id,
                    name: this.name ? this.name : this.$store.getters.CURRENT_CONFIGURATION.name
                };
                this.$store.dispatch('SAVE_CONFIGURATION', payload)
            }
        },
        computed: {
            configurationItems() {
                return this.$store.getters.PRODUCTS;
            }
        }
    }
</script>

<style lang="scss">
    .category-card {
        height: 450px;
        .v-image {
            height: 250px;
        }
    }
</style>