<script setup lang="ts">
  import Complaint from "~/pages/Complaint.vue";
  import { checkIfUserIsLoggedIn } from "~/helpers/authenticationCheck";
  import swal from 'sweetalert2';

  interface Complaint {
    reason: string;
    offerId: number;
    waybillNumber: number;
    driverPhone: number;
    name: string;
    surname: string;
    email: string;
    phone: number;
    message: string;
    productValue: number;
    damagedProductsValue: number;
    dateTimeOfIssue: string;
    accountNumber: number;
    nameOfPersonHandlingTheComplaint: string;
    surnameOfPersonHandlingTheComplaint: string;
    phoneOfPersonHandlingTheComplaint: number;
    emailOfPersonHandlingTheComplaint: string;
    proposalOfTheClientsClaimOrSolutionToTheTopic: string;
  }

  const form = reactive<Complaint>({} as Complaint);

  const config = useRuntimeConfig().public;
  const processing = ref<boolean>(false);
  const { $shopApi: shopApi } = useNuxtApp();
  const route = useRoute();
  const images = ref<FileList | null>(null);
  const avaibleReasons = [
    {
      label: 'zaginięcie przesyłki',
      value: 'zaginięcie przesyłki',
    },
    {
      label: 'uszkodzenie przesyłki',
      value: 'uszkodzenie przesyłki',
    },
    {
      label: 'brak zawartości przesyłki',
      value: 'brak zawartości przesyłki',
    },
    {
      label: 'opóźnienie przesyłki',
      value: 'opóźnienie przesyłki',
    },
    {
      label: 'skarga/inne',
      value: 'skarga/inne',
    },
    {
      label: 'odwołanie do reklamacji',
      value: 'odwołanie do reklamacji',
    },
    {
      label: 'uzupełnienie zgłoszenia',
      value: 'uzupełnienie zgłoszenia',
    }
  ];
  const packages = ref<any>([]);

  onMounted(() => {
    checkIfUserIsLoggedIn('Proszę się zalogować');
    getdataFromUrl();
  });

  watch(() => form.offerId, () => {
    fetchPackages();
  });

  const getdataFromUrl = () => {
    form.offerId = route.query.offerId as unknown as number;
  };

  const fetchPackages = async () => {
    const { data: response } = await shopApi.get(`/api/get-packages-for-order/${form.offerId}`) as any;

    packages.value = response.data.map((item: any) => {
      return {
        label: item.letter_number,
        value: item.id,
      }
    });
  };

  const submitFrom = async () => {
    processing.value = true;
    try {
      const { data: response } = await shopApi.post(`api/createCustomerComplaintChat/${form.offerId}`, dataToFormObject());

      await swal.fire(
          'Formularz został wysłany',
          'Dziękujemy za wypełnienie formularza',
          'success'
      )
    } catch (error) {
        await swal.fire({
          icon: 'error',
          title: 'Błąd',
          text: 'Wypełnij wszystkie pola',
        });
    } finally {
      processing.value = false;
    }
  }

  const dataToFormObject = () => {
    const formData = new FormData();
    formData.append('firstname', form.name);
    formData.append('surname', form.surname);
    formData.append('email', form.email);
    formData.append('phone', form.phone?.toString());
    formData.append('reason', form.reason);
    formData.append('date', form.dateTimeOfIssue);

    if (images.value) {
      for (let i = 0; i < images.value.length; i++) {
        formData.append('images', images.value[i]); // Append each selected image
      }
    }

    formData.append('driverPhone', form.driverPhone?.toString());
    formData.append('trackingNumber', form.waybillNumber?.toString());
    formData.append('accountNumber', form.accountNumber?.toString());
    formData.append('description', form.message);
    formData.append('offerId', form.offerId?.toString());
    formData.append('productValue', form.productValue?.toString());
    formData.append('damagedProductsValue', form.damagedProductsValue?.toString());
    formData.append('nameOfPersonHandlingTheComplaint', form.name);
    formData.append('surnameOfPersonHandlingTheComplaint', form.surname);
    formData.append('phoneOfPersonHandlingTheComplaint', form.phone?.toString());
    formData.append('emailOfPersonHandlingTheComplaint', form.email);
    formData.append('proposalOfTheClientsClaimOrSolutionToTheTopic', form.proposalOfTheClientsClaimOrSolutionToTheTopic);

    return formData;
  }
</script>

<template>
  <form @submit.prevent="submitFrom" class="w-[70%] mx-auto mt-10 shadow-lg p-8">
    <h1 class="text-4xl font-bold mb-10">
      Formularz reklamacyjny
    </h1>

    <p class="font-bold text-lg">
      Uwaga w przypadku nieprawidłowego działania formularza prosimy skontaktować z działem IT wsparcie techniczne pod numerem 691801594 ( 7-23)
    </p>

    <SelectInput
        v-model="form.reason"
        :options="avaibleReasons"
        label="Powód reklamacji"
    />

    <TextInput
        label="Numer oferty"
        type="number"
        :value="form.offerId"
        @input="form.offerId = $event"
        class="mt-4"
    />

    <SelectInput
        v-model="form.waybillNumber"
        :options="packages"
        label="Numer listu przewozowego"
    />

    <TextInput
        label="Numer telefonu kierowcy (w przypadku gdy jest znany)"
        :value="form.driverPhone"
        @input="form.driverPhone = $event"
        class="mt-4"
    />

    <TextInput
        type="text"
        label="Imię osoby obsługującej reklamację po stronie kupującego"
        :value="form.name"
        @input="form.name = $event"
        class="mt-4"
    />

    <TextInput
        label="Nazwisko osoby obsługującej reklamację po stronie kupującego"
        type="text"
        :value="form.surname"
        @input="form.surname = $event"
        class="mt-4"
    />

    <TextInput
        label="Email osoby obsługującej reklamację po stronie kupującego"
        type="email"
        :value="form.email"
        @input="form.email = $event"
        class="mt-4"
    />

    <TextInput
        label="Numer telefonu osoby obsługującej reklamację po stronie kupującego"
        type="number"
        :value="form.phone"
        @input="form.phone = $event"
        class="mt-4"
    />

    <TextInput
        label="Wiadomość / opis usterki"
        type="text"
        :value="form.message"
        @input="form.message = $event"
        class="mt-4"
    />

    <TextInput
        label="Wartość całej oferty"
        type="number"
        :value="form.productValue"
        @input="form.productValue = $event"
        class="mt-4"
    />

    <TextInput
        label="Wartość uszkodzonych produktów"
        type="number"
        :value="form.damagedProductsValue"
        @input="form.damagedProductsValue = $event"
        class="mt-4"
    />

    <TextInput
        label="Data i czas stwierdzenia uszkodzenia towaru"
        type="datetime-local"
        :value="form.dateTimeOfIssue"
        @input="form.dateTimeOfIssue = $event"
        class="mt-4"
    />

    <TextInput
        label="Numer konta bankowego do zwrotu należności"
        type="number"
        :value="form.accountNumber"
        @input="form.accountNumber = $event"
        class="mt-4"
    />

    <TextInput
      label="Propozycja roszczenia klienta lub rozwiązania tematu"
      type="text"
      :value="form.proposalOfTheClientsClaimOrSolutionToTheTopic"
      @input="form.proposalOfTheClientsClaimOrSolutionToTheTopic = $event"
      class="mt-4"
    />

    <div>
      <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
        Zdjęcie uszkodzonego towaru
      </label>
      <input
          type="file"
          id="images"
          multiple
          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          @change="images = $event.target.files"
      />
    </div>

    <SubmitButton class="mt-8" :disabled="processing">Wyślij</SubmitButton>
  </form>
</template>
