<template>
    <v-layout row text-xs-center>
        <v-flex xs12>
            <v-btn
                    @click="getConfigurationItems"
                    :color="this.$store.getters.CURRENT_CONFIGURATION && this.$store.getters.CURRENT_CONFIGURATION.status === 0 ? 'warning' : 'success'"
            >
                {{ this.$store.getters.CURRENT_CONFIGURATION && this.$store.getters.CURRENT_CONFIGURATION.status === 0 ? 'Resume' : 'Create' }} configuration
            </v-btn>
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
                                            v-model="configurationName"
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
                                <div>{{ currentConfigurationState.total_price }}</div>
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
                    <v-btn icon dark @click="closeProductsDialog">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>{{ categoryTitle }}</v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <v-container grid-list-md text-xs-center class="container__relative">
                        <v-layout row wrap>
                            <v-flex md2 style="height: 100%;" class="hidden-sm-and-down">
                                <v-container fluid>
                                    <div v-for="(property, id) in properties" class="mb-4" :key="id">
                                        <h3>{{ property.title }}</h3>
                                        <v-checkbox v-for="(value, key) in property.values"
                                                    :label="value"
                                                    :value="JSON.stringify({id,value})"
                                                    :key="key"
                                                    :disabled="(property.disabledValues).includes(value)"
                                                    v-model="selectedProperty"
                                                    @change="getProducts(true)"
                                                    hide-details></v-checkbox>
                                    </div>
                                </v-container>
                            </v-flex>
                            <v-flex sm12 md10>
                                <div v-infinite-scroll="getProducts" infinite-scroll-disabled="busy" infinite-scroll-distance="10">
                                    <v-layout row wrap justify-center>
                                        <v-flex xs12 sm6 md4 lg3 v-for="(product, key) of products" :key="key">
                                            <product-card :product="product"></product-card>
                                        </v-flex>
                                    </v-layout>
                                </div>
                            </v-flex>
                        </v-layout>
                        <v-layout v-if="busy" row wrap justify-center class="progress-modify">
                            <v-progress-circular
                                    indeterminate
                                    color="primary"
                            ></v-progress-circular>
                        </v-layout>
                        <v-layout row wrap justify-center>
                            <v-progress-circular v-if="busy"
                                    indeterminate
                                    color="primary"
                            ></v-progress-circular>
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
                busy: false,
                nextProductPage: null,
                products: [],
                properties: [],
                selectedProperty: [],
                categoryId: null,
                categoryTitle: null,
                name: 'New configuration',
                type: ['Home', 'Gaming', 'Office'],
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
            EventBus.$on('select-product', id => {
                this.nextProductPage = 1;
                this.dialog2 = true;
                this.categoryId = id;
                this.getProducts();
            });
            EventBus.$on('close-dialog', () => {
                this.closeProductsDialog();
            });
            EventBus.$on('get-configuration', () => {
                this.dialog = true;
            })
        },
        methods: {
            getConfigurationItems() {
                this.dialog = true;
                this.$store.dispatch('CREATE_CONFIGURATION');
            },
            saveConfiguration() {
                this.dialog = false;
                this.$store.dispatch('SAVE_CONFIGURATION', this.$store.getters.CURRENT_CONFIGURATION);
            },
            getProducts(filters = false) {
                if (!this.busy && this.nextProductPage || filters) {
                    this.busy = true;

                    axios.get('/api/products', {
                        params: {
                            category_id: this.categoryId,
                            configuration_id: this.$store.getters.CURRENT_CONFIGURATION.id,
                            property: this.selectedProperty || null,
                            page: filters ? 1 : this.nextProductPage || null,
                        }
                    }).then(response => {
                            const { products, properties, pagination } = response.data;

                            if (filters) {
                                this.products = products;

                                const propertiesCame = [];
                                for (let property in properties) {
                                    if (properties.hasOwnProperty(property)) {
                                        propertiesCame.push(property);
                                        const values = properties[property].values.map(value => value);
                                        this.properties[property].disabledValues = this.properties[property].values
                                            .map(value => value)
                                            .filter(value => !values.includes(value));
                                    }
                                }
                                Object.keys(this.properties)
                                    .filter(key => !propertiesCame.includes(key))
                                    .forEach(key => {
                                        this.properties[key].disabledValues = this.properties[key].values;
                                });
                            } else {
                                this.products.push(...products);
                            }
                            if (this.nextProductPage === 1) {
                                this.categoryTitle = products[0].category;

                                Object.keys(properties).forEach(key => {
                                    properties[key].disabledValues = [];
                                });
                                this.properties = properties;
                            }
                            this.nextProductPage = pagination.next || null;
                            this.busy = false;

                    })
                }
            },
            closeProductsDialog() {
              this.dialog2 = false;
              this.products = [];
              this.properties = [];
              this.selectedProperty = [];
            },
        },
        computed: {
            configurationItems() {
                return this.$store.getters.PRODUCTS;
            },
            configurationName: {
                get() {
                    return this.$store.getters.CURRENT_CONFIGURATION ? this.$store.getters.CURRENT_CONFIGURATION.name : 'New configuration'
                },
                set(value) {
                    this.$store.getters.CURRENT_CONFIGURATION.name = value;
                    this.$store.commit('SET_CURRENT_CONFIGURATION', this.$store.getters.CURRENT_CONFIGURATION);
                }
            },
            currentConfigurationState() {
                return this.$store.getters.CURRENT_CONFIGURATION ? this.$store.getters.CURRENT_CONFIGURATION : 0;
            }
        },
    }
</script>

<style lang="scss" scoped>
    .category-card {
        height: 450px;
        .v-image {
            height: 250px;
        }
    }
    .container__relative {
        position: relative;
    }
    .progress-modify {
        position: absolute;
        top: 0;
        height: 100vh;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(255,255,255,.4);
    }
</style>