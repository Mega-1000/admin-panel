<script setup>
import Swal from 'sweetalert2';
import { ref, computed, onBeforeMount } from 'vue';

const zipCode = ref('');
const zipCodeValid = ref(true);

onBeforeMount(() => {
  zipCode.value = localStorage.getItem('zipCode') || '';
});

const validateZipCode = (code) => {
  const zipCodePattern = /^[0-9]{2}(-?[0-9]{3})?$/;
  return zipCodePattern.test(code);
};

const formatZipCode = (code) => {
  const digitsOnly = code.replace(/\D/g, '');

  if (digitsOnly.length <= 2) {
    return digitsOnly;
  } else {
    return `${digitsOnly.slice(0, 2)}-${digitsOnly.slice(2, 5)}`;
  }
};

const zipCodeErrorMessage = computed(() => {
  return zipCodeValid.value ? '' : 'Kod pocztowy musi być w formacie xx-xxx';
});

const submitZipCode = async () => {
  const formattedZipCode = formatZipCode(zipCode.value);
  zipCodeValid.value = validateZipCode(formattedZipCode);
  if (!zipCodeValid.value) {
    return;
  }

  zipCode.value = formattedZipCode;
  localStorage.setItem('zipCode', formattedZipCode);

  await Swal.fire('Zapisano kod pocztowy', 'Od teraz będziemy ci pokazywać jedynie oferty w twoim zasięgu', 'success');
  await window.location.reload();
};
</script>

<template>
</template>

<style>
/* Define enter and leave animations */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
  opacity: 0;
}
.modal-backdrop {
  /* Your existing styles for backdrop */
  background-color: rgba(0, 0, 0, 0.17);
  /* Ensure full viewport coverage for the fading effect */
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal-content {
  /* Your existing styles for modal content */
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
</style>
