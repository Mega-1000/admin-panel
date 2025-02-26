<script setup lang="ts">
  const currentPage = ref(0);
  const startX = ref(0);
  const nextPageThreshold = 0.5; 
  const isDragging = ref(false);
  
  const pages = reactive([
    {
      title: 'Gazetka',
      content: 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euismod, nisl vel tincidunt luctus, nisl nisl aliquam nisl, vel aliquam nisl nisl sit amet lorem. Sed euism'
    },
    {
      title: 'Gazetka',
      content: 'okej'
    },    {
      title: 'Gazetka',
      content: '111'
    },
    {
      title: 'Gazetka',
      content: '22'
    },
  ])

  onMounted(() => {
    const url = new URL(window.location.href);
    const page = url.searchParams.get('page');
    if (page) {
      currentPage.value = parseInt(page);
    }
  });

  const currentPageData = computed(() => {
    const startIndex = currentPage.value > 0 ? currentPage.value - 1 : currentPage.value;
    const endIndex = Math.min(startIndex + 2, pages.length);
    return pages.slice(startIndex, endIndex);
  });

  const setUrl = () => {
    const url = new URL(window.location.href);
    url.searchParams.set('page', currentPage.value.toString());
    window.history.pushState({}, '', url.toString());
  };

  const nextPage = () => {
    currentPage.value += 2;
    setUrl();
  };

  const prevPage = () => {
    currentPage.value-= 2;
    setUrl();
  };
</script>

<template>
  <div>
    <div class="w-[80%] mx-auto ">
      <h1 class="mt-8 text-4xl font-semibold mb-10">Gazetka</h1>

      <div class="flex select-none">
        <div class="flex-1 mx-auto shadow-md p-10" v-for="page in currentPageData">
          {{ page.title }}
          <div v-html="page.content" class="flex gap-10"></div>
        </div>
      </div>
    </div>
    
    <div class="mt-20 text-center">
      <primaryButton class="w-[10%]" :disabled="currentPage === 0" @click="prevPage"> prev </primaryButton>
      <primaryButton class="w-[10%] ml-4" :disabled="currentPage + 2 >= pages.length" @click="nextPage"> next </primaryButton>
    </div>
  </div>
</template>

<style>
.container {
  transition: transform 0.5s ease-in-out;
}

</style>
