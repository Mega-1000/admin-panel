<template>
  <NewspaperNavbar />

  <div class="w-[80%] mx-auto mt-8">
    Strona spersonalizowana

    <div class="flex gap-10 mb-20">
      <NewspaperItemCard
          image="https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__480.jpg"
          title="Lorem ipsum dolor sit amet, 3"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam, asperiores."
          old-price="100"
          new-price="50"
      />

      <NewspaperItemCard
          image="https://cdn.pixabay.com/photo/2015/04/23/22/00/tree-736885__480.jpg"
          title="Lorem ipsum dolor sit amet, 3"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam, asperiores."
          old-price="100"
          new-price="50"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
const { $shopApi: shopApi } = useNuxtApp();
</script>
