<script setup>
import { Tabs } from "flowbite";
import OrderItem from "~~/components/account/OrderItem.vue";
import {checkIfUserIsLoggedIn} from "~/helpers/authenticationCheck";

const { $shopApi: shopApi } = useNuxtApp();

// Fetch orders function with pagination
const currentPage = ref(1);
const totalPages = ref(0);
const currentTab = ref('active');
let orders = reactive({ active: [], inactive: [], all: [] });

const navigationLink = ref(null);
const profileLink = ref(null);
const settingsLink = ref(null);
const logoutLink = ref(null);
const navLinks = ref(null);

const tutorialActive = ref(false);
const tutorialTitle = ref('');
const tutorialDescription = ref('');
const tutorialHighlightStyle = reactive({});
const tutorialNextButtonText = ref('Next');
const tutorialStep = ref(0);
const loading = ref(false);


// Adjusted fetchOrders to directly update the orders ref
const fetchOrders = async (page) => {
  try {
    loading.value = true;
    const inactiveStatusIds = [6, 8];
    const res = await shopApi.get(`/api/orders/getAll?page=${page}`);
    totalPages.value = res.data.last_page;
    orders.active = res.data.data.filter(order => !inactiveStatusIds.includes(order.status.id) && order.is_hidden === 0);
    orders.inactive = res.data.data.filter(order => inactiveStatusIds.includes(order.status.id) && order.is_hidden === 1);
    orders.all = res.data.data;
    loading.value = false;
  } catch (e) {
    console.error(e);
    loading.value = false;
  }
};

watch(currentPage, (newPage) => {
  fetchOrders(newPage);
});

onMounted(async () => {
  await checkIfUserIsLoggedIn();

  await fetchOrders(currentPage.value);
  showTutorial();

});

const goToPage = (page) => {
  currentPage.value = page;
};

const changeTab = (tabName) => {
  currentTab.value = tabName;
};


const showTutorial = () => {
  if (localStorage.getItem('tutorialEnded')) {
    return;
  }
  tutorialActive.value = true;

  switch (tutorialStep.value) {
    case 0:
      tutorialTitle.value = 'Witamy w panelu użytkownika!';
      tutorialDescription.value = 'W tym tutorialu pokażemy ci jak kożystać z twojego konta w portalu EPH Polska.';
      tutorialNextButtonText.value = 'Start';
      break;
    case 1:
      tutorialTitle.value = 'Grupy zamówień';
      tutorialDescription.value = 'W tej sekcji możesz wybrać grupę zamówień. Domyślnie jest to "Aktywne".';
      const navigationLinkRect = navigationLink.value.getBoundingClientRect();
      tutorialHighlightStyle.top = navigationLinkRect.top + window.pageYOffset + 'px';
      tutorialHighlightStyle.left = navigationLinkRect.left + window.pageXOffset + 'px';
      tutorialHighlightStyle.width = navigationLinkRect.width + 'px';
      tutorialHighlightStyle.height = navigationLinkRect.height + 'px';
      tutorialNextButtonText.value = 'Następny krok';
      break;
    case 2:
      tutorialTitle.value = 'Edycja danych powiązanych z kontem';
      tutorialDescription.value = 'Kliknij tutaj aby edytować adresy i zarządzać danymi powiązanymi z twoim kontem.';
      const profileLinkRect = profileLink.value.getBoundingClientRect();
      tutorialHighlightStyle.top = profileLinkRect.top + window.pageYOffset + 'px';
      tutorialHighlightStyle.left = profileLinkRect.left + window.pageXOffset + 'px';
      tutorialHighlightStyle.width = profileLinkRect.width + 'px';
      tutorialHighlightStyle.height = profileLinkRect.height + 'px';
      break;
    case 3:
      document.querySelector('.tutorial-modal').style.top = 0;
      document.querySelector('.tutorial-modal').style.position = 'fixed';

      tutorialTitle.value = 'Zarządzanie zamówieniami i faktura proforma';
      tutorialDescription.value = 'W tym module możesz zarządzać swoimi ofertami. Możesz dodać potwierdzenie przelewu, edytować ofertę, pobrać fakturę proformę i wiele więcej!';
      const settingsLinkRect = settingsLink.value.getBoundingClientRect();
      tutorialHighlightStyle.top = settingsLinkRect.top + window.pageYOffset + 'px';
      tutorialHighlightStyle.left = settingsLinkRect.left + window.pageXOffset + 'px';
      tutorialHighlightStyle.width = settingsLinkRect.width + 'px';
      tutorialHighlightStyle.height = settingsLinkRect.height + 'px';
      tutorialNextButtonText.value = 'Zakończ';
      break;
    case 4:
      localStorage.setItem('tutorialEnded', true);
      tutorialActive.value = false;
  }
};

const nextTutorialStep = () => {
  if (tutorialStep.value === 4) {
    tutorialActive.value = false;
  } else {
    tutorialStep.value++;
    showTutorial();
  }
};

</script>

<template>
  <div class="mb-5 border-b border-gray-200 mt-5 flex justify-center" ref="navigationLink">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-black" id="tabExample" role="tablist">
      <li class="mr-2" role="presentation">
        <button @click="changeTab('active')" :class="{'border-b-2 border-blue-500': currentTab === 'active'}" class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:border-gray-300" role="tab">
          Aktywne
        </button>
      </li>
      <li class="mr-2" role="presentation">
        <button @click="changeTab('inactive')" :class="{'border-b-2 border-blue-500': currentTab === 'inactive'}" class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:border-gray-300" role="tab">
          Nieaktywne
        </button>
      </li>
      <li class="mr-2" role="presentation">
        <button @click="changeTab('all')" :class="{'border-b-2 border-blue-500': currentTab === 'all'}" class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:border-gray-300" role="tab">
          Wszystkie
        </button>
      </li>
    </ul>
  </div>

  <div class="max-w-7xl mx-auto">
    <span ref="profileLink">
      <SubmitButton class="max-w-7xl mx-auto my-4">
        <nuxt-link href="/EditAccountInformations">
          Edytuj adresy i dane powiązane z twoim kontem
        </nuxt-link>
      </SubmitButton>
    </span>
  </div>

  <div id="tabContentExample" class="pb-20" ref="settingsLink">
    <div v-if="currentTab === 'active'" id="active-content" role="tabpanel">
      <div class="grid space-y-10">
        <div class="flex justify-center" v-for="(order, index) in orders.active" :key="order.id">
          <div>
            <OrderItem :item="order" />
          </div>
        </div>
      </div>
    </div>
    <div v-if="currentTab === 'inactive'" id="inactive-content" role="tabpanel">
      <div class="grid space-y-10">
        <div class="flex justify-center" v-for="order in orders.inactive" :key="order.id">
          <OrderItem :item="order" />
        </div>
      </div>
    </div>
    <div v-if="currentTab === 'all'" id="all-content" role="tabpanel">
      <div class="grid space-y-10">
        <div class="flex justify-center" v-for="order in orders.all" :key="order.id">
          <OrderItem :item="order" />
        </div>
      </div>
    </div>
  </div>

  <nav aria-label="Page navigation" class="flex justify-center mt-4 mb-16">
    <ul class="inline-flex items-center -space-x-px">
      <li :class="{ 'opacity-50': currentPage === 1 }">
        <button
            class="py-2 px-3 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
            @click="currentPage > 1 && goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
        >
          Poprzednia
        </button>
      </li>
      <li v-for="page in totalPages" :key="page" :class="{ 'bg-blue-500 border-blue-500 text-white': currentPage === page, 'border-gray-300 text-gray-500 hover:bg-gray-100': currentPage !== page }">
        <button
            class="py-2 px-3 leading-tight bg-white border hover:text-gray-700"
            @click="goToPage(page)"
        >
          {{ page }}
        </button>
      </li>
      <li :class="{ 'opacity-50': currentPage === totalPages }">
        <button
            class="py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700"
            @click="currentPage < totalPages && goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
        >
          Następna
        </button>
      </li>
    </ul>
  </nav>

  <div class="tutorial-highlight" style="position: fixed; z-index: 888" :style="tutorialHighlightStyle" v-if="tutorialActive">
    <slot name="tutorial-highlight"></slot>
  </div>

  <div v-if="tutorialActive" class="tutorial-overlay">
    <div class="tutorial-modal">
      <div class="tutorial-content">
        <h3 class="text-2xl font-bold">{{ tutorialTitle }}</h3>
        <p>{{ tutorialDescription }}</p>
        <SubmitButton @click="nextTutorialStep" class="mt-4">{{ tutorialNextButtonText }}</SubmitButton>
      </div>
    </div>
  </div>

  <div v-if="loading" class="fixed top-0 left-0 w-screen h-screen flex justify-center items-center bg-gray-500 bg-opacity-50">
    <Loader :showLoader="loading" />
  </div>
</template>


<style scoped>
/* Styles for the user dashboard */
.user-dashboard {
  display: flex;
}

.sidebar {
  width: 200px;
  background-color: #f1f1f1;
  padding: 20px;
}

.logo {
  text-align: center;
  margin-bottom: 20px;
}

nav ul {
  list-style-type: none;
  padding: 0;
}

nav a {
  display: block;
  padding: 10px;
  text-decoration: none;
  color: #333;
}

.main-content {
  flex: 1;
  padding: 20px;
}

/* Styles for the tutorial modals */
.tutorial-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 887;
}

.tutorial-modal {
  background-color: #fff;
  padding: 20px;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  text-align: center;
  position: relative;
  z-index: 900; /* Add a higher z-index value */
}

.tutorial-modal-container {
  position: relative;
}

.tutorial-highlight {
  position: absolute;
  background-color: rgba(255, 255, 0, 0.2);
  border-radius: 4px;
}

.tutorial-highlight::before {
  content: "";
  position: absolute;
  top: -10px;
  left: -10px;
  right: -10px;
  bottom: -10px;
  border: 2px dashed #ff0;
  border-radius: 6px;
  animation: highlight 1s infinite;
}

@keyframes highlight {
  0% {
    box-shadow: 0 0 0 0 rgba(255, 255, 0, 0.5);
  }
  50% {
    box-shadow: 0 0 0 10px rgba(255,255, 0, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(255, 255, 0, 0.5);
  }
}
</style>
