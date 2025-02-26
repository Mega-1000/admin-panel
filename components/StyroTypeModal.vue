<template>
  <div>
    Wybierz rodzaj styropianu
    <button
        @click="isOpen = true"
        class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
    >
      <span v-if="selected" class="font-medium text-gray-800">{{ selected }}</span>
      <span v-else class="text-gray-500">Kliknij aby wybrać rodzaj styropianu</span>
      <span class="float-right text-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </span>
    </button>

    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
      <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 ease-in-out" :class="{ 'scale-100 opacity-100': isOpen, 'scale-95 opacity-0': !isOpen }">
          <div class="p-6">
            <div class="flex justify-between items-center mb-6">
              <h2 class="text-2xl font-bold text-gray-800">Wybierz rodzaj styropianu</h2>
              <button @click="isOpen = false" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <input
                type="text"
                v-model="searchTerm"
                placeholder="Szukaj..."
                class="w-full p-3 border border-gray-300 rounded-lg mb-6 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <div class="max-h-[60vh] overflow-y-auto pr-2 space-y-6">
              <div v-for="(types, category) in filteredTypes" :key="category">
                <h3 class="font-bold text-lg mb-3 text-gray-700">{{ category }}</h3>
                <div class="grid gap-4 grid-cols-1">
                  <div
                      v-for="type in types"
                      :key="type"
                      class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer transform hover:-translate-y-1"
                      :class="{ 'ring-2 ring-green-500': selected === type }"
                      style="margin-left: 2px"
                      @click="handleSelect(type)"
                  >
                    <h4 class="text-lg font-semibold mb-2 text-gray-800">{{ type }}</h4>
                    <p class="text-sm text-gray-600 mb-2">Kategoria: {{ category }}</p>
                    <p class="text-sm text-gray-600">
                      Zastosowanie: {{ getApplicationInfo(category) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
import axios from "axios";
import { ref, computed, onMounted, watch } from 'vue';

export default {
  props: {
    modelValue: String,
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const isOpen = ref(false);
    const selected = ref(props.modelValue);
    const searchTerm = ref('');
    const styrofoamTypes = ref({});

    onMounted(() => {
      axios.get('https://admin.mega1000.pl/auctions/get-styrofoam-types').then(response => {
        styrofoamTypes.value = response.data;
      });
    });

    const filteredTypes = computed(() => {
      const searchTermLower = searchTerm.value.toLowerCase();
      return Object.entries(styrofoamTypes.value).reduce((acc, [category, types]) => {
        const filteredTypes = types.filter(type =>
            type.toLowerCase().includes(searchTermLower) ||
            category.toLowerCase().includes(searchTermLower)
        );
        if (filteredTypes.length > 0) {
          acc[category] = filteredTypes;
        }
        return acc;
      }, {});
    });

    const handleSelect = (type) => {
      selected.value = type;
      emit('update:modelValue', type);

      // Add a slight delay before closing the modal for a better visual effect
      setTimeout(() => {
        isOpen.value = false;
      }, 500);
    };

    const getApplicationInfo = (category) => {
      switch (category) {
        case '100.styropiany elewacyjne':
          return 'Izolacja ścian zewnętrznych';
        case '10.styropiany podłogowe':
          return 'Izolacja podłóg i dachów';
        case '10.styropiany wodoodporne':
          return 'Izolacja w miejscach narażonych na wilgoć';
        default:
          return '';
      }
    };

    watch(() => props.modelValue, (newValue) => {
      selected.value = newValue;
    });

    return {
      isOpen,
      selected,
      searchTerm,
      filteredTypes,
      handleSelect,
      getApplicationInfo
    };
  }
};
</script>

<style scoped>
/* Custom scrollbar styles */
.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 20px;
  border: transparent;
}
</style>
