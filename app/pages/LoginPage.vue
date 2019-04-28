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
                            <v-text-field prepend-icon="person" name="login" label="Login" type="text"></v-text-field>
                            <v-text-field id="password" prepend-icon="lock" name="password" label="Password" type="password"></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary">Login</v-btn>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                is_logged_in: false,
                current_user: null,
                login: '',
                password: '',
                remember_me: 0,
                login_error: '',
                password_error: ''
            }
        },
        methods: {
            attemptLogin() {
                this.login_error = '';
                this.password_error = '';

                if (!this.login.length) {
                    this.login_error = 'Username cannot be blank.';
                }

                if (!this.password.length) {
                    this.password_error = 'Password cannot be blank.';
                }

                if(this.password_error.length == 0 && this.login_error.length == 0) {
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
                        this.refreshCSRFToken(response.data.token);
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
