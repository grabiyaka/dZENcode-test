<template>
<div >
    <div @click="modal = true" :class="btnClass">
        <i class="bi bi-box-arrow-in-right"></i>
        sign_in
    </div>
    <form :class="{ active: modal }" class="box" action="" method="post">
        <h2>sign_in</h2>
        <input v-model="email" type="email" placeholder="email" />
        <input v-model="password" type="password" placeholder="password" />
        <input type="checkbox" v-model="remember" name="remember" value="1" id="remember" />
        <label for="remember">remember_me</label>
        <input @click.prevent="login" type="submit" name="sing_up" :disabled="loading"  />
    </form>
    <div @click="modal = false" class="md-overlay" :class="{ active: modal }"></div>
</div>
</template>

  
<style>
  </style>

  
<script>
import axios from "axios";

export default {
    name: "Login",

    props: ["btnClass"],

    mounted() {},

    data() {
        return {
            email: null,
            password: null,
            errors: null,
            modal: false,
            loading: false,
            remember: false,
        };
    },

    methods: {
        login() {
            // this.$store.commit("setLoading", true);
            axios.get("/sanctum/csrf-cookie").then((response) => {
                axios
                    .post("/login", {
                        email: this.email,
                        password: this.password,
                        remember: this.remember,
                    })
                    .then((res) => {
                        this.modal = false
                        // this.$store.commit("setLoading", false);
                        localStorage.setItem(
                            "x_xsrf_token",
                            res.config.headers["X-XSRF-TOKEN"]
                        );
                        this.$store.dispatch("getToken");
                        this.$router.push({
                            name: "cabinet"
                        });
                    })
                    .catch((err) => {
                        // this.$store.commit("setLoading", false);
                        if (err.response.status == 422) {
                            alert('wrong login or password');
                        }
                    });
            });
        },
    },
};
</script>
