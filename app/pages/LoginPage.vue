<template>
    <v-container fill-height>
        <v-layout align-center justify-center>
            <v-flex xs12 sm8 md6>
                <v-card class="elevation-12">
                    <v-toolbar dark color="primary">
                        <v-toolbar-title>Login form</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                    v-model="email"
                                    v-validate="'required|email'"
                                    :error-messages="errors.collect('email')"
                                    data-vv-name="email"
                                    prepend-icon="person"
                                    name="email"
                                    label="E-mail"
                                    type="email"
                                    required
                            ></v-text-field>
                            <v-text-field
                                    v-model="password"
                                    v-validate="'required|min:6|max:16'"
                                    :error-messages="errors.collect('password')"
                                    data-vv-name="password"
                                    prepend-icon="lock"
                                    name="password"
                                    label="Password"
                                    type="password"
                                    required
                            ></v-text-field>
                            <v-checkbox
                                    v-model="remember_me"
                                    value="1"
                                    label="Remember me"
                                    data-vv-name="checkbox"
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
                is_logged_in: false,
                current_user: null,
                email: '',
                password: '',
                remember_me: null,
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
            }
        },
        methods: {
            attemptLogin() {
                this.$validator.validateAll()
                    .then(result => {
                        if (result) {
                            axios.post('/api/login', {
                                username: this.email,
                                password: this.password,
                                rememberMe: this.remember_me
                            }).then(response => {
                                const { data } = response;
                                this.refreshCSRFToken(data.token);
                                if (data.result === 'success') {
                                    this.is_logged_in = true;
                                    this.current_user = data.user_id;
                                } else {
                                    console.log(data.messages);
                                    this.$validator.errors.add({
                                        field: String(Object.keys(data.messages)),
                                        msg: String(Object.values(data.messages))
                                    });
                                }
                            })
                        }
                    });


                if(0) {
                    axios({
                        method: 'post',
                        url: '/api/login',
                        responseType: 'json',
                        data: {
                            username: this.login,
                            password: this.password,
                            rememberMe: this.remember_me
                        }
                    }).then((response) => {
                        //this.refreshCSRFToken(response.data.token);
                        if (response.data.result == 'success') {
                            this.is_logged_in = true;
                            this.current_user = response.data.user_id;
                        } else {
                            if (response.data.messages.password) {
                                this.password_error = response.data.messages.password;
                            }
                            if (response.data.messages.username) {
                                this.login_error = response.data.messages.username;
                            }
                        }
                })
                }
            },
            refreshCSRFToken(token) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            }
        }
    }
</script>
