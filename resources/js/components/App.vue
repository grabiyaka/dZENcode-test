<script>
import {
    RouterLink,
    RouterView
} from "vue-router";
import HeaderComponent from "./Main/HeaderComponent.vue";
import LoaderComponent from "./Main/LoaderComponent.vue";
import FooterComponent from "./Main/FooterComponent.vue";

export default {
    components: {
        HeaderComponent,
        LoaderComponent,
        FooterComponent
    },
    data: () => ({}),
    mounted() {
        this.$store.dispatch('fetchUser');
    }
};
</script>
    
<template>
<HeaderComponent ref="header" />
<router-view v-slot="{ Component }">
    <transition name="slide" mode="out-in">
        <div id="viewbox" :key="$route.path">
            <component :is="Component"></component>
        </div>
    </transition>
</router-view>
<LoaderComponent></LoaderComponent>
<FooterComponent />
</template>

<style lang="scss">
@import url("/node_modules/bootstrap-icons/font/bootstrap-icons.css");

$lightblue: #b2c8df;
$darkblue: #6e85b7;

#viewbox {
    padding: 10px;
    min-height: 70vh;
}

.border-block {
    border-bottom: 5px solid #8a8a8a;
    border-radius: 0;
}

#app {
    display: flex;
    flex-direction: column;
}

h1,
h2,
h3,
h4 {
    font-size: 36px;
    font-weight: bold;
    margin: 0 0 20px;
    color: #333;
    // font-family: Arial, sans-serif;
    text-align: center;
}

h1 {
    position: relative;
    font-size: 50px;
    font-weight: 600;
    background-image: linear-gradient(to left, #000, $darkblue);
    color: transparent;
    background-clip: text;
    -webkit-background-clip: text;
}

.underline {
    position: relative;

    &::after {
        content: "";
        display: block;
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 100%;
        height: 2px;
        background-image: linear-gradient(to right, transparent, $lightblue, transparent);
        opacity: 0.9;
    }
}

h2 {
    text-align: center;
    font-size: 28px;
    margin: 0 0 20px;
    color: #333;
}

h3 {
    text-align: center;
    font-size: 22px;
    margin: 0 0 20px;
    color: #333;
}

h4 {
    font-size: 18px;
    margin: 0 0 10px;
    color: #555;
}

router-view {
    margin-bottom: 50px;
}

button:disabled,
button[disabled] {
    border: 1px solid #999999;
    background-color: #cccccc;
    color: #666666;
}

.slide-enter-active {
    transition: all 0.3s ease;
}

.slide-leave-active {
    transition: all 0.4s ease;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    overflow: hidden;
    transform: scale(0.9);
}

#content {
    font-family: "Roboto", sans-serif;
    flex-direction: column;
    flex-wrap: wrap;
}

.images-box {
    display: flex;
    flex-wrap: wrap;
}

input[type="file"] {
    display: none;
    /* Скрываем оригинальный input */
}

label.file-upload-button {
    font-size: 16px;
    background: #b2c8df;
    border-radius: 50%;
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    border: none;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    /* Добавляем красивую тень */
    outline: none;
    transition: all ease 0.5s;
    position: relative;
}

label.file-upload-button::before {
    content: "+";
    font-size: 36px;
    /* Увеличиваем размер плюсика */
    color: rgba(0, 0, 0, 0.6);
    /* Делаем плюсик прозрачным */
    position: absolute;
}

/* Позиционируем плюсик в середине кнопки */
label.file-upload-button::before {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

label.file-upload-button:hover {
    background: #6e85b7;
    color: white;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    /* Увеличиваем тень при наведении */
}

.image-box {
    align-items: center;
    justify-content: center;
    text-align: center;
    width: 80%;
    margin: 0;
}

img {
    width: calc(90vh * 3 / 4);
    //height: auto;
    max-width: 100%;
    margin: 0;
}

.container {
    justify-content: center;
    display: flex;
    padding: 0;
}

form {
    text-align: center;
    margin-top: 50px;
}

.checkboxs {
    width: fit-content;
}

.checkboxs input[type="radio"] {
    margin-left: 3%;
    margin-right: 3%;
}

input[type="text"],
input[type="number"],
.select {
    font-size: 18px;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;

    &.select_80 {
        width: 80%;
    }
}

input[type="submit"] {
    width: 80%;
    height: 40px;
    background-color: #4caf50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all ease 0.5s;
}

input[type="submit"]:hover {
    background-color: #45a049;
}

div {
    border-radius: 5px;
}

body {
    height: 100%;
    min-height: 100vh;
    font-size: 17px;
    margin-left: auto;
    margin-right: auto;
}

.cross-delete {
    position: absolute;
    top: -15px;
    right: 5px;
    font-size: 20px;
    color: whitesmoke;
    border: none;
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: transform 0.5s;
    background: #d9534f;
    padding: 0;
}

.cross-delete:hover {
    transform: rotate(180deg);
}

.ellipsis-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 320px;
}

.btn {
    background-color: #007bff;
    color: #fff;
    padding: 5px 10px;
    border: none;
    cursor: pointer;
    margin-right: 10px;
    border-radius: 5px;
}

.delete-btn {
    background-color: #d9534f;

    &:hover {
        background-color: #b64441;
    }
}

.reply-btn {
    background-color: #5bc0de;

    &:hover {
        background-color: #4695ad;
    }
}

.gray-btn {
    background-color: #808080;

    &:hover {
        background-color: #6b6b6b;
    }
}
</style>
