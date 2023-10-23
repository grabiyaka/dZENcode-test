<template>
<div  id="header">
    <div class="background"></div>
    <ul class="ui-header">
        <li :class="{optional: !$store.state.token.token}">
            <RouterLink class="nav-el" :to="{ name: 'home' }"><i class="bi bi-house-door-fill"></i> home
               </RouterLink>
        </li>

        <LoginComponent class="" v-if="!$store.state.token.token" :btnClass="'nav-el'"></LoginComponent>

        <RegistrationComponent class="" v-if="!$store.state.token.token" :btnClass="'nav-el'">
        </RegistrationComponent>
        <li class="optional" v-if="$store.state.token.token">
            <a class="nav-el" @click.prevent="logout"><i class="bi bi-door-open-fill"></i>exit
                </a>
        </li>
        <li class="" v-if="$store.state.token.token">
            <RouterLink class="nav-el" :to="{ name: 'cabinet' }"><i class="bi bi-person-circle"></i> cabinet
                </RouterLink>
        </li>
        
    </ul>
</div>
</template>

<script>
import axios from 'axios';
import {
    RouterLink,
    RouterView
} from 'vue-router'
import LoginComponent from './../User/LoginComponent.vue'
import RegistrationComponent from './../User/RegistrationComponent.vue'

export default {

    components: {
        LoginComponent,
        RegistrationComponent
    },

    data() {
        return {
            signIn: false,
            signUp: false,
            email: null,
            password: '',
            errors: null,
            name: null,
            password_confirmation: null,
        }
    },
    updated() {

    },
    mounted() {
        this.$store.dispatch('getToken')
    },
    methods: {
        logout() {
            if (confirm('?')) {
                axios.post('/logout')
                    .then(res => {
                        localStorage.removeItem('x_xsrf_token')
                        this.$store.dispatch('getToken')
                        this.$router.push({
                            name: 'home'
                        })
                    })
            }
        },
    },
    computed: {
        lang: {
            get() {
                return this.$store.getters.getLang
            },
            set(lang) {
                this.$store.commit('setLang', lang)
            }
        }
    },

};
</script>

<style lang="scss">
body {
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif
}

@media screen and (max-width: 475px) {
    .optional {
        display: none;
    }
}

#header .background {
    width: 100%;
    padding-top: 25px;
    padding-bottom: 45px;
}

.ui-header {
    top: 0;
    z-index: 50;
    position: fixed;
    width: 100%;
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #ffffff;
    padding: 10px;
    justify-content: center;
    text-align: center;
    display: flex;
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.3),
}

.lang {
    margin-left: auto;
    margin-right: 40px;
    align-self: flex-end;
}

.lang select {
    display: block;
    font-size: 16px;
    font-family: sans-serif;
    font-weight: 700;
    color: #444;
    line-height: 1.3;
    padding: .6em 1.4em .5em .8em;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    margin: 0;
    border: 1px solid #aaa;
    box-shadow: 0 1px 0 1px rgba(0, 0, 0, .04);
    border-radius: .5em;
    -moz-appearance: none;
    -webkit-appearance: none;
    appearance: none;
    background-color: #fff;
    background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'), linear-gradient(to bottom, #ffffff 0%, #e5e5e5 100%);
    background-repeat: no-repeat, repeat;
    background-position: right .7em top 50%, 0 0;
    background-size: .65em auto, 100%;
}

#header li {
    float: left;
}

.nav-el {
    display: block;
    color: black;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    cursor: pointer;
    border-radius: 5px;
    background: #ffffff;
    transition: all ease .3s;
}

/* Change the link color to #111 (black) on hover */
.nav-el:hover {
    background-color: #6e85b7;
    color: white;
}

body {
    margin: 0;
    padding: 0;
    font-family: sans-serif;
}

.box {
    visibility: hidden;
    opacity: 0;
    left: 50%;
    top: 50%;
    position: fixed;
    width: 30px;
    padding: 40px;
    border: 2px solid #6E85B7;
    transform: translateX(-50%) translateY(-50%);
    background: #FFF5EE;
    text-align: center;
    border-radius: 10px;
    transition: 0.5s;
    backface-visibility: hidden;
}

.box.active {
    z-index: 200;
    opacity: 1;
    visibility: visible;
    width: 300px;
}

.box h1 {
    color: black;
    text-transform: uppercase;
    font-weight: 500;
}

.box input[type="text"],
.box input[type="password"],
.box input[type="email"] {
    border: 0;
    background: none;
    display: block;
    margin: 20px auto;
    text-align: center;
    border: 2px solid #B2C8DF;
    padding: 10px 8px;
    width: 200px;
    color: black;
    outline: none;
    border-radius: 16px;
    transition: 0.25s;
}

.box input[type="text"]:focus,
.box input[type="password"]:focus,
.box input[type="email"]:focus {
    width: 280px;
    border-color: #6E85B7;
}

.box input[type="submit"] {
    border: 0;
    background: none;
    display: block;
    margin: 20px auto;
    text-align: center;
    border: 2px solid #B2C8DF;
    padding: 10px 35px;
    width: 200px;
    color: black;
    outline: none;
    border-radius: 16px;
    transition: 0.25s;
    cursor: pointer;
}

.box button {
    border: 0;
    background: none;
    display: block;
    margin: 20px auto;
    text-align: center;
    border: 2px solid #C4D7E0;
    padding: 10px 35px;
    width: 200px;
    color: black;
    outline: none;
    border-radius: 16px;
    transition: 0.25s;
    cursor: pointer;
}

.box button:hover {
    background: #B2C8DF;
    color: white;
}

.box input[type="submit"]:hover {
    background: #6E85B7;
    color: white;
}

.md-overlay {
    position: fixed;
    width: 100%;
    height: 100%;
    visibility: hidden;
    top: 0;
    left: 0;
    z-index: 100;
    opacity: 0;
    background: rgba(128, 128, 128, 0.651);
    transition: all 0.3s;
}

.md-overlay.active {
    opacity: 1;
    visibility: visible;
}
</style>
