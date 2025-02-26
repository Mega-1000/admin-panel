<script setup lang="ts">
  import { checkIfUserIsLoggedIn } from "~/helpers/authenticationCheck";
  import StyrofoarmTable from "~/components/StyrofoarmTable.vue";

  const {$shopApi: shopApi } = useNuxtApp();
  const tableData = ref<boolean | typeof StyrofoarmTable>(false);
  const postalCode = ref('');
  const processing = ref(false);
  const router = useRouter();
  const route = useRoute();

  onMounted(() => {
    if (route.query.postalCode) {
      postalCode.value = route.query.postalCode as string;
      generateTable();
    }
  })

  const savePostalCode = () => {
    router.push({ query: { postalCode: postalCode.value } });

    checkIfUserIsLoggedIn('Aby kontynuować musisz być zalogowany').then(() => {
      generateTable();
    });
  }

  const generateTable = async () => {
    processing.value = true;
    try {
      const res = await shopApi.get('api/styrofoarm/generate-tables/' + postalCode.value);

      tableData.value = res.data as typeof StyrofoarmTable;
    } catch (error) {
      console.log(error);
    } finally {
      processing.value = false;
    }
  }
</script>

<template>
  <div class="mt-10 w-[70%] mx-auto shadow-lg p-8">
    <div v-if="!tableData">
      <h1 class="font-semibold text-2xl">
        Podaj kod pocztowy a wyświetlimy ci ofertę wszystkich producentów którzy mogą dostarczyć ci towar.
      </h1>

      <form @submit.prevent="savePostalCode" class="mt-8">
        <TextInput placeholder="Wyszukaj" class="w-full" @input="postalCode = $event" />

        <SubmitButton class="mt-4" :disabled="processing">
          Wyszukaj
        </SubmitButton>
      </form>
    </div>

    <StyrofoarmTable v-else :tableData="tableData" />
  </div>
</template>
