<template>
    <router-view></router-view>
    <div class="portal">
        <h1>admin</h1>
    </div>
</template>

<script>
export default {
    name: 'Portal',
}
</script>

<style scoped>
.portal {
    padding: 2rem;
    text-align: center;
}
</style>