<template>
    <main
        role="main"
        class="container flex-center position-ref full-height"
    >
        <div class="top-right links">
            <template v-if="$auth.check()">
                <router-link :to="{ name: 'Dashboard' }">
                    {{ $t('home') }}
                </router-link>
            </template>
            <template v-else>
                <router-link :to="{ name: 'Login' }">
                    {{ $t('auth.login.title') }}
                </router-link>
                <router-link :to="{ name: 'Register' }">
                    {{ $t('auth.register.title') }}
                </router-link>
            </template>
        </div>
        <div class="text-center content">
            <child />
        </div>
    </main>
</template>

<script setup>
import Child from '../components/Child.vue'
</script>

<style lang="scss" scoped>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200;300;400;500;600;700;800;900;1000&display=swap');
html, body {
    background-color: #fff;
    color: #636b6f;
    font-family: 'Nunito', sans-serif;
    
    // height: 100vh;
    margin: 0;
}
.full-height {
    height: 100vh;
}
.flex-center {
    align-items: center;
    display: flex;
    justify-content: center;
}
.position-ref {
    position: relative;
}
.top-right {
    position: absolute;
    right: 10px;
    top: 18px;
}
.content {
    text-align: center;
}
.links > a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
}
.m-b-md {
    margin-bottom: 30px;
}
</style>
