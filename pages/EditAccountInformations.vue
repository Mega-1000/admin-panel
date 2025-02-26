<script setup lang="ts">
  import { checkIfUserIsLoggedIn } from "~/helpers/authenticationCheck";
  import AccountEditData from "~/components/account/AccountEditData.vue";
  import {getToken, removeCookie} from "~/helpers/authenticator";
  import Swal from 'sweetalert2';

  const { $shopApi: shopApi } = useNuxtApp();

  type editMode = "password" | "standard_address" | "primary_data" | "invoice_data" | "shipment_data";

  interface Address {
    id: number;
    name: string;
    surname: string;
    street: string;
    house_number: string;
    flat_number: string;
    city: string;
    postal_code: string;
    phone: string;
    email: string;
    type: string;
  }

  const user = ref<any>({});
  const password = ref("");
  const processing = ref(false);
  const error = ref("");
  const editMode = ref<editMode>("password");
  const router = useRouter();
  const standardAddress = ref({});
  const success = ref(false);
  const passwordConfirmation = ref("");
  const invoiceAddress = ref({});
  const shipmentAddress = ref({});

  const getAddress = (name: string) => {
    return user.value.addresses.filter(
        (address: { type: string; }) => address.type === name
    )[0] as Address ?? {
      name: "",
      surname: "",
      street: "",
      house_number: "",
      flat_number: "",
      city: "",
      postal_code: "",
      phone: "",
      email: "",
    } as Address;
  };

  onMounted(async () => {
    await checkIfUserIsLoggedIn(
      "Proszę się zalogować, aby móc zmienić hasło i dane do przesyłki"
    );

    ({ data: user.value } = await shopApi.get("/api/user"));

    standardAddress.value = getAddress("STANDARD_ADDRESS");
    invoiceAddress.value = getAddress("INVOICE_ADDRESS");
    shipmentAddress.value = getAddress("DELIVERY_ADDRESS");

    initEditMode();
  });

  const savePassword = async () => {
    processing.value = true;

    if (password.value !== passwordConfirmation.value) {
      error.value = "Hasła nie są takie same";
      processing.value = false;

      setTimeout(() => {
        error.value = "";
      }, 3000);

      return;
    }

    try {
      await shopApi.post("/api/user/change-password", {
        password: password.value,
      });
      setSuccess(true);
    } catch (e: any) {
      error.value = e.response.data.message;

      setTimeout(() => {
        error.value = "";
      }, 3000);
    }
    processing.value = false;
  };

  const setEditMode = (mode: editMode) => {
    editMode.value = mode;

    router.push({ query: { editMode: mode } });
  }

  const initEditMode = () => {
    const queryEditMode = router.currentRoute.value.query.editMode;
    switch (queryEditMode) {
      case "password":
        editMode.value = "password";
        break;
      case "standard_address":
        editMode.value = "standard_address";
        break;
      case "invoice_data":
        editMode.value = "invoice_data";
        break;
      case "shipment_data":
        editMode.value = "shipment_data";
        break;
    }
  }

  const setSuccess = (value: boolean) => {
    success.value = value;

    setTimeout(() => {
      success.value = false;
    }, 3000);
  }

  const SubmitAddresses = async () => {
    processing.value = true;
    await shopApi.put("/api/user/update", {
      standardAddress: standardAddress.value,
      invoiceAddress: invoiceAddress.value,
      deliveryAddress: shipmentAddress.value,
    });

    setSuccess(true);

    processing.value = false;
  }

  const unregister = async () => {
    // sure?
    Swal.fire({
      title: 'Czy jesteś pewien?',
      text: "Ta akcja nie będzie odwracalna w przyszłości!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Tak, wyrejestruj mnie!',
      cancelButtonText: 'Nie, anuluj!'
    }).then(async (result) => {
      if (result.isConfirmed) {
        await shopApi.post("/api/user/unregister");

        await Swal.fire(
            'Wyrejestrowano!',
            'Nie będziesz już mógł się zalogować na swoje konto. Jeśli to pomyłka skontaktuj się z nami.',
            'success'
        )

        await removeCookie();
        await router.go(0);
      }
    })
  }
</script>

<template>
  <div class="mt-10 w-[70%] mx-auto rounded bg-white shadow p-8">
    <button class="px-4 h-fit py-2 bg-red-500 rounded text-white mb-4" @click="unregister">
      Wyrejestruj się
    </button>

    <div v-if="error" class="mb-8 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
      <strong class="font-bold">Błąd!</strong>
      <span class="block sm:inline">{{ error }}</span>
    </div>

    <div class="mb-8">
      <SubmitButton @click="setEditMode('password')" :disabled="editMode === 'password'">
        Zmień hasło
      </SubmitButton>

      <SubmitButton class="ml-4" @click="setEditMode('standard_address')" :disabled="editMode === 'standard_address'">
        Adres podstawowy
      </SubmitButton>

      <SubmitButton class="ml-4" @click="setEditMode('invoice_data')" :disabled="editMode === 'invoice_data'">
        Dane do faktury
      </SubmitButton>

      <SubmitButton class="ml-4" @click="setEditMode('shipment_data')" :disabled="editMode === 'shipment_data'">
        Dane do wysyłki
      </SubmitButton>
    </div>

    <form @submit.prevent="savePassword" v-if="editMode === 'password'">
      <label for="password_input">Hasło</label>
      <TextInput id="password_input" placeholder="Hasło - Jeśli chcesz je zmienić wpisz je tutaj, w przeciwnym wypadku zostaw puste" :value="password" @input="password = $event" ></TextInput>

      <label for="password_confirmation_input">Potwierdź hasło</label>
      <TextInput id="password_confirmation_input" placeholder="Potwierdź hasło" :value="passwordConfirmation" @input="passwordConfirmation = $event" ></TextInput>

      <SubmitButton class="mt-4" type="submit" :disabled="processing">Zapisz</SubmitButton>
    </form>

    <form @submit.prevent="SubmitAddresses" v-if="editMode === 'standard_address'">
      <AccountEditData address-type="STANDARD_ADDRESS" :success="success" :address="standardAddress" :editMode="editMode" @input="standardAddress = $event" />

      <submitButton :success="success" :disabled="processing" class="mt-8" :class="{ 'bg-geen-500': success }">
        Zapisz
      </submitButton>
    </form>

    <form @submit.prevent="SubmitAddresses" v-if="editMode === 'invoice_data'">
      <AccountEditData address-type="INVOICE_ADDRESS" :success="success" :address="invoiceAddress" :editMode="editMode" @input="invoiceAddress = $event" />

      <SubmitButton :disabled="processing" class="mt-8" :class="{ 'bg-geen-500': success }">
        Zapisz
      </submitButton>
    </form>

    <form @submit.prevent="SubmitAddresses" v-if="editMode === 'shipment_data'">
      <AccountEditData address-type="DELIVERY_ADDRESS" :success="success" :address="shipmentAddress" :editMode="editMode" @input="shipmentAddress = $event" />
      <SubmitButton :disabled="processing" class="mt-8" :class="{ 'bg-geen-500': success }">
        Zapisz
      </submitButton>
    </form>
  </div>
</template>
