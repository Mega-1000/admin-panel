<template>
  <div class="md:w-2/3 mx-auto">
    <div class="flex items-center mb-4">
      <span
          v-for="(_, index) in 5"
          :key="index"
          class="text-3xl text-yellow-400 cursor-pointer"
          @click="updateRating(index + 1)"
      >
        <span v-if="rating > index" class="text-yellow-500">&#9733;</span>
        <span v-else>&#9734;</span>
      </span>
    </div>
    <div>
      <textarea
          v-model="description"
          class="w-full p-2 border border-gray-300 rounded-md"
          placeholder="Tu wpisz swoją opinię"
          rows="4"
      ></textarea>
    </div>

    <SubmitButton @click="submitForm" class="mb-16">
      Zapisz opinię
    </SubmitButton>
  </div>
</template>

<script setup>
  import swal from "sweetalert2";

  const props = defineProps(['productId']);

  const rating = ref(0);
  const description = ref('');
  const { $shopApi: shopApi } = useNuxtApp();

  const updateRating = (value) => {
    rating.value = value;
  }

  const submitForm = async () => {
    await shopApi.post('/api/productOpinion/create', {
      product_id: props.productId,
      rating: rating.value,
      text: description.value,
      user_name: 'Użytkownik',
    });

    await swal.fire('Dodano komentarz', '', 'success').then(() => {
      window.location.reload();
    })
  }
</script>
