<script setup>
const route = useRoute();
const isNoLayout = ref(false);

onMounted(() => {
  const lastSegment = route.path.split('/').pop();

  if (lastSegment === 'no-layout') {
    isNoLayout.value = true;
  }
})

</script>

<template>
  <Navbar v-if="!isNoLayout" />
  <NuxtPage />
  <Footer v-if="!isNoLayout" />
</template>

<style>
.page-enter-active,
.page-leave-active {
  transition: all 0.4s cubic-bezier(0.250, 0.460, 0.450, 0.940);
  transform: translate3d(0,0,0);
}
.page-enter-from {
  opacity: 0;
  transform: translate3d(0,-100%,0);
}
.page-leave-to {
  opacity: 0;
  transform: translate3d(0,100%,0);
}
</style>
