<script setup lang="ts">
const props = defineProps<{
  categories: any[];
  categoryTree?: any[];
  nested?: boolean;
}>();
</script>

<template>
  <div
      :class="`lg:block w-15 md:w-25 h-fit w-full bg-white hidden md:block ${
      !nested ? 'shadow rounded' : ''
    }`"
      id="sidenavExample"
  >
    <h3
        v-if="!nested"
        class="text-xl lg:text-2xl py-4 px-6 font-semibold bg-gray-100 rounded-t-lg"
    >
      Kategorie
    </h3>
    <ul class="divide-y divide-gray-200">
      <li
          id="sidenavEx2"
          v-for="category in props.categories"
          class="uppercase"
      >
        <NuxtLink
            :class="`py-3 px-6 ${
            nested && 'pl-8'
          } flex items-center text-md lg:text-lg overflow-hidden text-gray-700 hover:text-white hover:bg-cyan-500 transition duration-300 ease-in-out cursor-pointer ${
            categoryTree?.map((category: any) => category.id)[
              categoryTree.length - 1
            ] === category.id
              ? 'bg-cyan-500 font-semibold text-white'
              : ''
          }`"
            data-mdb-ripple="true"
            data-mdb-ripple-color="dark"
            :href="`/${category.rewrite}/${category.id}`"
        >
          <span v-if="nested" class="mr-2">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
              <path
                  fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd"
              />
            </svg>
          </span>
          {{ category.name }}
        </NuxtLink>
        <div
            v-if="categoryTree?.map((category: any) => category.id).includes(category.id) && category.children"
        >
          <Sidebar
              :name="`${category.id}`"
              :categories="category.children"
              :category-tree="categoryTree"
              nested
          />
        </div>
      </li>
    </ul>
  </div>
</template>
