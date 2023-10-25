<template lang="">
    <div>
        Your Cabinet
    </div>
</template>
<script>
export default {
    mounted() {
        this.$store.dispatch('fetchUser');
    }
}
</script>
<style lang="">
    
</style>