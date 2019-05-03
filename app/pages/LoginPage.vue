<template>
    <v-container fill-height>
        <v-layout align-center justify-center>
            <v-flex xs12 sm8 md6>
                <v-card class="elevation-12" v-if="!isLoggedIn">
                    <v-toolbar dark color="primary">
                        <v-toolbar-title>Login form</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                    v-model="form.email"
                                    v-validate="'required|email'"
                                    :error-messages="errors.collect('login-form-login')"
                                    data-vv-name="login-form-login"
                                    prepend-icon="person"
                                    name="login-form-login"
                                    label="E-mail"
                                    type="email"
                                    autocomplete="username"
                                    required
                            ></v-text-field>
                            <v-text-field
                                    v-model="form.password"
                                    v-validate="'required|min:6|max:16'"
                                    :error-messages="errors.collect('login-form-password')"
                                    data-vv-name="login-form-password"
                                    prepend-icon="lock"
                                    name="login-form-password"
                                    label="Password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                            ></v-text-field>
                            <v-checkbox
                                    v-model="form.remember_me"
                                    value="1"
                                    label="Remember me"
                                    data-vv-name="login-form-rememberme"
                                    type="checkbox"
                                    required
                            ></v-checkbox>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn @click="attemptLogin" color="primary" :disabled="errors.any() || isFormInvalid">Login</v-btn>
                    </v-card-actions>
                </v-card>
                <v-card v-if="isLoggedIn">
                    <v-alert
                            :value="true"
                            type="success"
                            transition="scale-transition"
                    >
                        Your are champion.
                    </v-alert>
                    <v-card-actions>
                        Username: {{ getUser.username }}, ID{{ getUser.id }}
                        <v-spacer></v-spacer>
                        <v-btn color="warning" @click="attemptLogout">Logout</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        $_veeValidate: {
            validator: 'new'
        },
        data() {
            return {
                current_user: null,
                form: {
                    email: '',
                    password: '',
                    remember_me: null,
                },
                dictionary: {
                    custom: {
                        email: {
                            required: 'Email can not be empty'
                            // custom messages
                        }
                    }
                }
            }
        },
        mounted() {
            this.$validator.localize('en', this.dictionary);
        },
        computed: {
            isFormInvalid() {
                return Object.keys(this.fields).some(key => this.fields[key].invalid);
            },
            isLoggedIn() {
                return this.$store.getters.LOGGED;
            },
            getUser() {
                return this.$store.getters.USER;
            }
        },
        methods: {
            attemptLogin() {
                this.$validator.validateAll()
                    .then(result => {
                        if (result) {
                            axios.post('/api/login', {
                                login: this.form.email,
                                password: this.form.password,
                                rememberMe: this.form.remember_me
                            }).then(response => {
                                const { data } = response;
                                this.refreshCSRFToken(data.token);
                                if (data.result === 'success') {
                                    this.$store.dispatch('GET_CONFIGURATIONS');
                                    this.$store.dispatch('GET_LOGGED');
                                    this.current_user = data.user_id;
                                } else {
                                    for (const error in data.result) {
                                        if (data.result.hasOwnProperty(error)) {
                                            this.$validator.errors.add({
                                                field: String(error),
                                                msg: String(data.result[error]),
                                            })
                                        }
                                    }
                                }
                            })
                        }
                    });
            },
            attemptLogout() {
              axios.get('/api/logout').then(response => {
                  const { data } = response;
                  if (data.logout) {
                      this.$store.dispatch('GET_CONFIGURATIONS');
                      this.$store.dispatch('GET_LOGGED');
                  } else {
                      console.error('error');
                  }
              })
            },
            refreshCSRFToken(token) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            }
        }
    }
</script>
