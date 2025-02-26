<script setup lang="ts">
  import LastAddressesModal from "~/components/account/LastAddressesModal.vue";

  const props = defineProps<{
    address: any;
    success: boolean;
    addressType: string;
  }>();

  const emit = defineEmits<{
    (event: 'input', value: any): void;
    (event: 'getAddress', value: any): void;
  }>();

  watch(() => props.address, (newVal) => {
    emit('input', newVal);
  });
</script>

<template>
  <div class="mb-4">
    <LastAddressesModal :addressType="addressType" @selectAddress="address = $event.addresses.adress" />
  </div>

  <label for="name_input">Imię</label>
  <TextInput id="name_input" placeholder="Imię" :value="address.firstname" @input="address.firstname = $event" ></TextInput>

  <!-- surname -->
  <label for="surname_input">Nazwisko</label>
  <TextInput id="surname_input" placeholder="Nazwisko" :value="address.lastname" @input="address.lastname = $event" ></TextInput>

  <!-- firmname -->
  <label for="firmname_input">Nazwa firmy</label>
  <TextInput id="firmname_input" placeholder="Nazwa firmy" :value="address.firmname" @input="address.firmname = $event" ></TextInput>

  <!-- nip -->
  <label for="nip_input">NIP</label>
  <TextInput id="nip_input" placeholder="NIP" :value="address.nip" @input="address.nip = $event" ></TextInput>

  <!-- phone_number -->
  <label for="phone_number_input">Numer telefonu</label>
  <TextInput id="phone_number_input" placeholder="Numer telefonu" :value="address.phone" @input="address.phone = $event" ></TextInput>

  <!-- adres -->
  <label for="address_input">Adres</label>
  <TextInput id="address_input" placeholder="Adres" :value="address.address" @input="address.address = $event" ></TextInput>

  <!-- building number -->
  <label for="building_number_input">Numer budynku</label>
  <TextInput id="building_number_input" placeholder="Numer budynku" :value="address.flat_number" @input="address.flat_number = $event" ></TextInput>

  <!-- city -->
  <label for="city_input">Miasto</label>
  <TextInput id="city_input" placeholder="Miasto" :value="address.city" @input="address.city = $event" ></TextInput>

  <!-- zip code -->
  <label for="zip_code_input">Kod pocztowy</label>
  <TextInput id="zip_code_input" placeholder="Kod pocztowy" :value="address.postal_code" @input="address.postal_code = $event" ></TextInput>

  <div v-if="success" class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
    <strong class="font-bold">Sukces!</strong>
    <span class="block sm:inline">Twoje dane zostały zaaktualizowane</span>
  </div>
</template>
