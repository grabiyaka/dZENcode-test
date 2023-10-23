<template lang="">
<div v-if="$store.getters.getLoading">
    <div class="preloader">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="loader-background"></div>
</div>
</template>

<script>
export default {

}
</script>

<style lang="scss">
.loader-background {
    border-radius: 0;
    margin: 0;
    padding: 0;
    top: 0;
    position: fixed;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.288);
    z-index: 300;
}

.preloader {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 100;
}

.preloader span {
    display: block;
    height: 15px;
    width: 15px;
    background: #19e3d5;
    margin: 0 7px;
    border-radius: 10px;
    animation: 1.2s upDown infinite;
}

.preloader span:first-child {
    animation-delay: .15s;
}

.preloader span:nth-child(2) {
    animation-delay: .3s;
}

.preloader span:nth-child(3) {
    animation-delay: .45s;
}

.preloader span:nth-child(4) {
    animation-delay: .6s;
}

.preloader span:last-child {
    animation-delay: .75s;
}

@keyframes upDown {
    0% {
        height: 15px;
        background: #19e3d5;
    }

    50% {
        height: 60px;
        background: #d33deb;
    }

    100% {
        height: 15px;
        background: #f00e43;
    }
}
</style>
