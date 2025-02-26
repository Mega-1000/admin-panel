<script setup>
const { $shopApi: shopApi } = useNuxtApp();

const categories = ref([]);
const formData = reactive({
  name: "",
  description: "",
  parent: "",
});
const router = useRouter();

onMounted(async () => {
  await fetchCategories();
});

const fetchCategories = async () => {
  const { data } = await shopApi.get("/api/products/categories");
  categories.value = data.map((category) => {
    return {
      label: category.name,
      value: category.id,
    };
  });

  categories.value.unshift({
    label: "-- Wybierz kategorię --",
    value: "",
  });

  categories.value = categories.value.concat(
    data.flatMap((category) => {
      return category.children.map((child) => {
        return {
          label: child.name,
          value: child.id,
        };
      });
    })
  );

  const deepLoop = (arr) => {
    arr.forEach((item) => {
      if (item.children) {
        categories.value = categories.value.concat(
          item.children.map((child) => {
            return {
              label: child.name,
              value: child.id,
            };
          })
        );
        deepLoop(item.children);
      }
    });
  };

  deepLoop(data);
};

const submitFrom = async () => {
  const { data } = await shopApi.post("/api/categories/create", {
    name: formData.name,
    description: formData.description,
    parent_id: Number(formData.parent),
  });

  router.push(`/${data.name}/${data.id}`);
};
</script>

<template>
  <form @submit.prevent="submitFrom" class="w-[50%] mx-auto mt-8">
    <TextInput :value="formData.name" @input="formData.name = $event" type="text" placeholder="Nazwa kategorii" class="w-full" label="nazwa" />

    <TextInput :value="formData.description" @input="formData.description = $event" type="text" placeholder="Opis kategorii" class="w-full mt-4" label="opis" />

    <SelectInput @update:modelValue="formData.parent = $event;" label="Kategoria rodzic" :options="categories" class="mt-4" />

    <div class="mt-4">
      <button class="bg-green-500 rounded text-white px-4 py-2">
        Zapisz
      </button>
    </div>
  </form>
</template>