<template>
  <div>
    <div class="max-w-[350px] bg-white border border-gray-200 rounded-lg shadow">
      <a href="#">
        <img class="rounded-t-lg" :src="image" alt="" />
      </a>
      <div class="p-5">
        <a href="#">
          <h5 class="mb-2 text-md font-bold tracking-tight text-gray-900">{{ title }}</h5>
        </a>
        <p class="my-4">
          Stara cena <span class="line-through">{{ oldPrice }} zł</span>
          <br>
          Nowa cena <span class="font-bold">{{ newPrice }} zł</span>
        </p>
        <p class="mb-3 font-small text-gray-700" v-html="description.replaceAll('|', '<br>')" />
        <nuxt-link :href="link">
          <submit-button>
            Pokaż więcej
          </submit-button>
        </nuxt-link>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string;
  description: string;
  image: string;
  link: string;
  oldPrice: number;
  newPrice: number;
}

defineProps<Props>();
</script>
