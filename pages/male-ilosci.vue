<template>
  <section class="py-24 px-4 bg-gray-100 animate-fade-in-up">
    <div class="container mx-auto text-center">
      <h2 class="text-3xl md:text-5xl font-bold mb-8 text-emerald-800">
        Odbiór w jednym z <em class="text-emerald-600">100</em> punktów w najniższych cenach prosto od producenta
      </h2>
      <h4 class="text-emerald-500 font-bold text-lg md:text-4xl mb-4">
        Kliknij na punkt aby sprawdzić dostępne w nim produkty
      </h4>
      <p class="text-gray-700 mb-12 text-lg">
        Ponad 10,000 zadowolonych klientów skorzystało z naszej sieci punktów odbioru!
      </p>
      <MagasinesMap class="shadow-2xl rounded-lg overflow-hidden" />
    </div>
  </section>

  <section class="py-24 px-4 bg-emerald-50 animate-fade-in-up">
    <div class="container mx-auto">
      <h2 class="text-4xl md:text-5xl font-bold mb-16 text-center text-emerald-800">
        Co mówią nasi klienci
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
        <div v-for="(testimonial, index) in testimonials" :key="index"
             class="bg-white rounded-lg shadow-lg p-8 transition-all duration-300 hover:shadow-xl hover:-translate-y-2">
          <div class="flex items-center mb-6">
            <img :src="testimonial.avatar" :alt="testimonial.name" class="w-16 h-16 rounded-full mr-4 border-4 border-emerald-300">
            <div>
              <h3 class="font-bold text-xl text-emerald-700">{{ testimonial.name }}</h3>
              <p class="text-emerald-600">{{ testimonial.location }}</p>
            </div>
          </div>
          <p class="text-gray-700 italic mb-6 text-lg">"{{ testimonial.quote }}"</p>
          <div class="flex items-center">
                <span v-for="i in 5" :key="i" class="text-yellow-400 mr-1">
                  <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                  </svg>
                </span>
            <span class="font-bold text-emerald-700 ml-2">{{ testimonial.rating }}/5</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <StyroTree />
</template>

<script setup>
const testimonials = [
  {
    name: "Anna Kowalska",
    location: "Warszawa",
    quote: "Dzięki przetargowi na Mega1000 zaoszczędziłam ponad 20% na styropianie do mojego domu. Polecam!",
    rating: 5,
    avatar: "/11.jpg"
  },
  {
    name: "Piotr Nowak",
    location: "Kraków",
    quote: "Proces był prosty i szybki. Otrzymałem wiele konkurencyjnych ofert w ciągu kilku godzin.",
    rating: 4.5,
    avatar: "/33.jpeg"
  },
  {
    name: "Marta Wiśniewska",
    location: "Wrocław",
    quote: "Świetna obsługa klienta i najlepsze ceny na rynku. Na pewno skorzystam ponownie!",
    rating: 5,
    avatar: "/22.jpeg"
  }
];
</script>
