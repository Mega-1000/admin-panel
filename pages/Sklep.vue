<script setup lang="ts">
import { defaultImgSrc } from "~~/helpers/buildImgRoute";
import Swal from "sweetalert2";
import ReferalBanner from "~/components/ReferalBanner.vue";

const { $shopApi: shopApi, $buildImgRoute: buildImgRoute } = useNuxtApp();
const route = useRoute();

const { data: categories, pending } = await useAsyncData(async () => {
  try {
    const res = await shopApi.get(`/api/products/categories?zip-code=${localStorage.getItem('zipCode')}&`);
    return res.data;
  } catch (e: any) {}
});

const isStaff = ref(false);

const buildLink = ({ rewrite, id }: { rewrite: string; id: number }) =>
    `/${rewrite}/${id}`;

onMounted(async () => {
  if (route.query.ref) {
    localStorage.setItem('registerRefferedUserId', atob(route.query.ref as string || ''));

    Swal.fire({
      title: 'Kod poleceń został zapisany!',
      text: 'Wchodzisz na stronę z kodem polecenia od twojego znajomego.',
      icon: 'success',
      confirmButtonText: 'OK'
    })
  }

  if (localStorage.getItem('noAllegroVisibilityLimit') == null) {
    localStorage.setItem('noAllegroVisibilityLimit', 'true');
    window.location.reload();
  }
  //
  // const data:any = await shopApi.get('/api/staff/isStaff');
  //
  // if (data.data) {
  //   isStaff.value = true;
  // }
  //
  // useState('isStaff', () => isStaff.value);
})

const config = useRuntimeConfig().public;
</script>

<template>
  <ReferalBanner />
  <LogosSection />
  <p v-if="pending">Loading...</p>
  <div v-else class="flex">
    <section class="pt-10 pb-20 w-full flex justify-center">
      <div>
        <div class="lg:flex justify-center">
          <div class="px-10 w-full lg:w-fit justify-center">
            <Sidebar
                class="h-fit flex flex-col justify-center mt-30 w-full"
                :categories="categories"
            />

            <nuxt-link class="bg-green-500 rounded px-4 py-2 text-white" href="/categories/create" v-if="isStaff">Dodaj kategorię</nuxt-link>
          </div>
          <div class="w-full flex justify-center">
            <div>
              <div
                  class="grid grid-cols-1 gap-6 p-6 sm:grid-cols-2 mb-10 items-center lg:grid-cols-3 h-fit"
              >
                <article
                    v-for="category in categories"
                    class="h-full w-full sm:w-auto rounded bg-white p-3 shadow hover:shadow-xl hover:transform hover:scale-105 duration-300"
                >
                  <div :class="category.blured ? 'blur-sm' : ''">
                    <NuxtLink :href="buildLink(category)">
                      <div class="overflow-hidden rounded-xl h-4/5">
                        <img
                            :src="buildImgRoute(category.img)"
                            alt="Photo"
                            @error="(e: any) => (e.target!.src = defaultImgSrc)"
                            class="w-full h-full"
                        />
                      </div>

                      <div class="mt-1 p-2 h-1/5">
                        <h2 class="text-gray-900 font-medium">
                          {{ category.name }}
                        </h2>
                      </div>
                    </NuxtLink>
                  </div>
                </article>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
