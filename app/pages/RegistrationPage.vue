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
                                    v-model="form.name"
                                    v-validate="'required'"
                                    :error-messages="errors.collect('name')"
                                    data-vv-name="name"
                                    prepend-icon="person"
                                    name="name"
                                    label="Your name"
                                    type="text"
                                    required
                            ></v-text-field>
                            <v-text-field
                                    v-model="form.email"
                                    v-validate="'required|email'"
                                    :error-messages="errors.collect('email')"
                                    data-vv-name="email"
                                    prepend-icon="email"
                                    name="email"
                                    label="E-mail"
                                    type="email"
                                    required
                            ></v-text-field>
                            <v-text-field
                                    v-model="form.password"
                                    v-validate="'required|min:6|max:16'"
                                    :error-messages="errors.collect('password')"
                                    hint="It should be a minimum of 6 characters"
                                    data-vv-name="password"
                                    data-vv-delay="300"
                                    prepend-icon="lock"
                                    ref="password"
                                    name="password"
                                    label="Password"
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
                                    data-vv-delay="300"
                                    name="password_confirmation"
                                    label="Password confirmation"
                                    type="password"
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
                form: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                },
                dictionary: {
                    custom: {
                        name: {
                          required: () => 'Name can not be empty'
                        },
                        password: {
                            required: () => 'Password can not be empty',
                            max: 'The password field may not be greater than 100 characters',
                            min: 'The password field may not be lesser than 6 characters'
                            // custom messages
                        },
                        password_confirmation: {
                            required: () => 'Password confirmation can not be empty',
                            confirmed: 'The password confirmation does not match'
                            // custom messages
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
                            const newUser = {
                                name: this.form.name,
                                email: this.form.email,
                                password: this.form.password
                            };

                            axios.post('/api/register/', {
                                name: this.form.name,
                                email: this.form.email,
                                password: this.form.password
                            }).then(response => console.log(response));
                            console.log(newUser);
                        }
                    })
            },
            refreshCSRFToken(token) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            }
        }
    }
</script>