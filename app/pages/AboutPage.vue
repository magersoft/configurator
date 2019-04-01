<template>
    <div class="container">
        <h1>About page</h1>
        <p>
            This is the About page. <a :href="url">Google</a>
        </p>
        <p v-on:mouseover="onHover">Counter: {{ counter }}</p>
        <button @click="riseCounter(5)">+ 5</button>
        <button @click="riseCounter(10)">+ 10</button>

        <input type="text" v-model="inputValue">
        <h2>{{ inputValue }}</h2>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                url: 'http://google.com',
                counter: 0,
                inputValue: 'v model'
            }
        },
        methods: {
            onHover(event) {
                event.target.style.color = 'red';
            },
            riseCounter(count) {
                this.counter += count;
            }
        }
    }
</script>
