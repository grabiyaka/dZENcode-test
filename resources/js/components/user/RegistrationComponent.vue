<template>
<div >
    <div @click="modal = true" :class="btnClass">
        <i class="bi bi-person-plus-fill"></i>
        sign_up
    </div>
    <form :class="{ active: modal }" class="box" action="" method="post">
        <h2>sign_up</h2>
        <input v-model="email" type="email" placeholder="email" required />
        <input v-model="name" type="text" placeholder="name" required />
        <input v-model="password" type="password" placeholder="password" required />
        <input v-model="password_confirmation" type="password" placeholder="password_confirmation" required />
        <input @click.prevent="register" type="submit" name="sing_up" :value="sign_up" />
    </form>
    <div @click="modal = false" class="md-overlay" :class="{ active: modal }"></div>
</div>
</template>

  
<script>
import axios from 'axios';

export default {
    name: "Registration",

    props: ['btnClass'],

    data() {
        return {
            email: null,
            name: null,
            password: null,
            password_confirmation: null,
            modal: false
        }
    },

    mounted() {
        let date = new Date
        date.setDate(date.getDate() + 14)
    },

    methods: {

        formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        },
        register() {
            if ((this.email !== null && this.email !== '') && (this.password !== null && this.password !== '')) {
                axios.get('/sanctum/csrf-cookie').
                then(response => {
                    let validEmail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                    let passw = /^[A-Za-z]\w{7,27}$/;
                    if (this.email.match(validEmail)) {
                        if (this.password.match(passw)) {
                            if (this.password === this.password_confirmation) {
                                if (this.name !== '' && this.name !== null) {
                                    let period = new Date
                                    period.setDate(period.getDate() + 14)
                                    axios.post('/register', {
                                            email: this.email,
                                            name: this.name,
                                            password: this.password,
                                            password_confirmation: this.password_confirmation,
                                            remember: 1,
                                            period: this.formatDate(period)
                                        })
                                        .then(res => {
                                            localStorage.setItem('x_xsrf_token', res.config.headers['X-XSRF-TOKEN'])
                                            this.$store.dispatch('getToken')
                                            this.$router.push({
                                                name: 'cabinet'
                                            })
                                        })
                                        .catch(err => {

                                        })
                                } else {
                                    alert(this.fill_name_field)
                                }
                            } else {
                                alert(this.password_not_confirmed)
                            }

                        } else {
                            alert(this.wrong_password);
                        }
                    } else {
                        alert(this.incorrect_email);
                    }
                })
            } else {
                alert(this.fill_fields)
            }
        },
    }
}
</script>

  
<style>
  </style>
