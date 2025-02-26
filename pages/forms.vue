<script>
import { onMounted } from "vue";
const route = useRoute();

const formName = route.query.formName;
const orderId = route.query.orderId;
console.log(formName);

setTimeout(() => {
  window.location.href = `https://admin.mega1000.pl/form/${formName}/${orderId}`;
}, 10)
</script>

<template>
</template>

