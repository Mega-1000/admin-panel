<script setup lang="ts">
const route = useRoute();

const contactContent = "Dziękujemy. Wkrótce odezwiemy się.";
const otherContent =
  "Dziękujemy za wysłanie zapytania o darmowy transport. \nW zależności od rodzaju zapytania skontaktujemy się z Panią/Panem do kilku godzin roboczych.\n";

const content = route.query.contact ? contactContent : otherContent;
</script>

<template>
  <div class="flex justify-center mb-20">
    <div class="mt-3 font-medium text-lg">
      <p>{{ content }}</p>
      <br />
      <p>
        Wszelkie pytania prosimy kierować poprzez nasz chat dostępny po
        zalogowaniu się na naszej platformie. Logowanie możliwe za pomocą emaila
        i hasła(numer telefonu)
      </p>
    </div>
  </div>
</template>
