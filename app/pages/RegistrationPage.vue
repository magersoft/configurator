<template>
    <v-container full-height>
        <v-layout align-center justify-center>
            <v-flex xs12 sm8 md6>
                <v-card class="elevation-12">
                    <v-toolbar dark color="primary">
                        <v-toolbar-title>Registration form</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                    v-model="form.username"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('register-form-username')"
                                    data-vv-name="register-form-username"
                                    prepend-icon="person"
                                    name="register-form-username"
                                    label="Your name"
                                    type="text"
                                    autocomplete="username"
                                    required
                            ></v-text-field>
                            <v-text-field
                                    v-model="form.email"
                                    v-validate="'required|email'"
                                    :error-messages="errors.collect('register-form-email')"
                                    data-vv-name="register-form-email"
                                    name="register-form-email"
                                    prepend-icon="email"
                                    label="E-mail"
                                    type="email"
                                    autocomplete="username"
                                    required
                            ></v-text-field>
                            <v-text-field
                                    v-model="form.password"
                                    v-validate="'required|min:6|max:16'"
                                    :error-messages="errors.collect('register-form-password')"
                                    hint="It should be a minimum of 6 characters"
                                    data-vv-name="register-form-password"
                                    prepend-icon="lock"
                                    ref="password"
                                    name="register-form-password"
                                    label="Password"
                                    autocomplete="new-password"
                                    :append-icon="show ? 'visibility' : 'visibility_off'"
                                    :type="show ? 'text' : 'password'"
                                    @click:append="show = !show"
                                    required
                            ></v-text-field>
                            <v-text-field
                                    v-model="form.password_confirmation"
                                    v-validate="'required|confirmed:password'"
                                    target= "password"
                                    :error-messages="errors.collect('password_confirmation')"
                                    prepend-icon="lock"
                                    data-vv-name="password_confirmation"
                                    name="password_confirmation"
                                    label="Password confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    :append-icon="showConfirmed ? 'visibility' : 'visibility_off'"
                                    :type="showConfirmed ? 'text' : 'password'"
                                    @click:append="showConfirmed = !showConfirmed"
                                    required
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn @click="attemptRegistration" color="primary" :disabled="errors.any() || isFormInvalid">Registration</v-btn>
                    </v-card-actions>
                </v-card>
                <v-alert
                        v-if="alert"
                        :value="true"
                        dismissible
                        type="success"
                        transition="scale-transition"
                >
                    Your account has been created.
                </v-alert>
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
                show: false,
                showConfirmed: false,
                alert: false,
                form: {
                    username: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                },
                dictionary: {
                    custom: {
                        name: {
                            required: () => 'Name can not be empty'
                        },
                        email: {
                            required: () => 'Email can not be empty',
                        },
                        password: {
                            required: () => 'Password can not be empty',
                            max: 'The password field may not be greater than 16 characters',
                            min: 'The password field may not be lesser than 6 characters'
                        },
                        password_confirmation: {
                            required: () => 'Password confirmation can not be empty',
                            confirmed: 'The password confirmation does not match'
                        }
                    }
                }
            }
        },
        mounted() {
            this.$validator.localize('en', this.dictionary)
        },
        computed: {
            isFormInvalid() {
                return Object.keys(this.fields).some(key => this.fields[key].invalid);
            }
        },
        methods: {
            attemptRegistration() {
                this.$validator.validateAll()
                    .then(result => {
                        if (result) {
                            axios.post('/api/register', {
                                username: this.form.username,
                                email: this.form.email,
                                password: this.form.password
                            }).then(response => {
                                const { data } = response;
                                this.refreshCSRFToken(data.token);
                                if (data.result === 'success') {
                                    this.alert = true;
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
                            });
                        }
                    })
            },
            refreshCSRFToken(token) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            }
        }
    }
</script>