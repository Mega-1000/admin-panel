<script setup lang="ts">
  import Swal from "sweetalert2";
  import { loginUser } from "~/helpers/loginUser";
  import validate from "~/helpers/validator";

  const form = reactive({
    processing: false,
    phone: "",
    email: "",
    errors: null as any,
  });

  const { $shopApi: shopApi } = useNuxtApp();
  const route = useRoute();
  const router = useRouter();

  onMounted(() => {
    form.email = route.query.email as string;
  })

  const submit = async () => {
    form.errors = null;

    form.errors = validate({
      phone: {
        required: true,
        pattern: /[0-9]{9}$/,
      }
    }, {
      phone: form.phone,
    });

    if (form.errors) {
      console.log(form.errors)
      return;
    }

    form.processing = true;
    try {
      await shopApi.post("/api/register", {
        password: form.phone,
        login: form.email
      });

      form.processing = false;

      await Swal.fire({
        title: "Zapisano",
        text: "Numer telefonu został zapisany kliknij OK aby przejść do chatu",
        icon: "success",
        confirmButtonText: "OK",
      });

      router.push("/faq");

      await loginUser(form.email, form.phone);
    } catch (e) {
      form.processing = false;
      await Swal.fire({
        title: "Błąd",
        text: "Wystąpił błąd podczas zapisywania numeru telefonu",
        icon: "error",
        confirmButtonText: "OK",
      });
    }
  };
</script>

<template>
  <div class="p-10 shadow-lg w-[70%] mx-auto mt-10">
    <form @submit.prevent="submit">
      <ValidationErrors label="Problemy z rządaniem" :options="form.errors" />

      <TextInput @input="form.phone = $event" label="Podaj swój numer telefonu" />

      <SubmitButton class="mt-4" :disabled="form.processing">
        Zapisz
      </SubmitButton>
    </form>
  </div>
</template>
