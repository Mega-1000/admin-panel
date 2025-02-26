<template v-if="item">
  <input class="font-black text-gray-800 md:text-3xl text-xl z-20" @input="handleInput" v-model="name" contenteditable="true">
  <div class="text-left w-full font-light text-sm">
    {{ item.symbol }}
    <div class="mt-4" @click="handleInput" >
      Zapisuj nazwę
      <input v-model="saveName" :checked="saveName" type="checkbox">
      {{ saveName }}

      <div class="mt-4" @click="handleInput" >
        Zapisuj zdjęcie
        <input v-model="saveImage" :checked="saveImage" type="checkbox">
        {{ saveImage }}

        <div class="mt-4">
          <input type="file" @change="handleInput" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
    item: any;
}

const props = defineProps<Props>();

const { $shopApi: shopApi } = useNuxtApp();
const name = ref(props.item.name);
const saveName = ref(props.item.save_name === 1);
const saveImage = ref(props.item.save_image === "1");

const handleInput = (e: any) => {
  setTimeout(() => {
    const form = new FormData();
    form.append('name', name.value);
    form.append('save_name', String(saveName.value));
    form.append('save_image', String(saveImage.value));
    form.append('image', e.target.files ? e.target?.files[0] : null);

    if (e.target.files && e.target.files[0]) {
      setTimeout(() => {
        window.location.reload();
      }, 1000);
    }
    shopApi.post(`/api/products/${props.item.id}`, form);
  }, 100);
};
</script>
