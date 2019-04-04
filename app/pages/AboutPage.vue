<template>
    <div class="container">
        <h1>About page</h1>
        <p>
            This is the About page. <a :href="url">Google</a>
        </p>
        <form>
            <div class="form-group" :class="{
                'has-error': $v.email.$error,
                'has-success': !$v.email.$invalid
            }">
                <label for="email" class="control-label">E-mail</label>
                <input
                    id="email"
                    type="email"
                    class="form-control"
                    @input="$v.email.$touch()"
                    v-model="email"
                >
                <div class="help-block" v-if="!$v.email.required">Email is required</div>
                <div class="help-block" v-if="!$v.email.email">Not email</div>
                <div class="help-block" v-if="$v.email.email">Success</div>
            </div>
            <div class="form-group" :class="{
                'has-error': $v.password.$error,
                'has-success': !$v.password.$invalid
            }">
                <label for="password" class="control-label">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    @input="$v.password.$touch()"
                    v-model="password"
                >
                <div class="help-block" v-if="!$v.password.minLength">Min length of password {{ $v.password.$params.minLength.min }} symbol. Now it is {{ password.length }}</div>
                <div class="help-block" v-if="$v.password.minLength">Success</div>
            </div>

            <button type="submit" class="btn btn-primary" :disabled="$v.$invalid">Send</button>
        </form>

    </div>
</template>

<script>
    import { required, email, minLength } from 'vuelidate/lib/validators';

    export default {
        data() {
            return {
                email: '',
                password: ''
            }
        },
        validations: {
            email: {
                required,
                email
            },
            password: {
                required,
                minLength: minLength(6)
            }
        },
        methods: {

        }
    }
</script>
