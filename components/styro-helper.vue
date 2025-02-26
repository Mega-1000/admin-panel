<template>
  <div class="container mx-4">
    <h2 class="title">Skorzystaj z pomocy sztucznej inteligencji i znajdź idealny styropian dla twojego projektu</h2>
    <div class="input-container">
      <input
          type="text"
          placeholder="Np: Potrzebuję czegoś na ocieplenie podłogi..."
          v-model="prompt"
          @keyup.enter="sendS"
          class="input"
      />
      <button class="button" @click="sendS" :disabled="!prompt || loading">
        <span v-if="loading" class="spinner"></span>
        <span v-else>Sprawdź</span>
      </button>
    </div>
    <div v-if="response" class="response-container" @animationend="stopAnimation">
      <div class="response-header">
        <h3 ref="typewriterTextRef"></h3>
      </div>
      <transition-group
          name="product-list"
          tag="div"
          class="product-list"
          @before-enter="beforeEnter"
          @enter="enter"
          @leave="leave"
      >
        <div
            v-for="(product, index) in response.products"
            :key="index"
            class="product-item"
        >
          <ProductItem
              :item="product.name"
              class="w-full"
              :showModal="true"
          />

          {{ product.description }}
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref, onMounted, nextTick } from "vue";

const prompt = ref("");
const response = ref(null);
const loading = ref(false);
const typewriterTextRef = ref(null);
const selectedProduct = ref(null);

const sendS = async () => {
  loading.value = true;
  const queryParams = new URLSearchParams(window.location.search);
  const url = `https://admin.mega1000.pl/api/styro-help`;

  try {
    const res = await axios.post(url, {
      message: prompt.value,
      formData: queryParams.toString(),
    });
    response.value = res.data;
    prompt.value = "";

    await nextTick();
    typewriteText(res.data.message);
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const typewriteText = async (text) => {
  if (typewriterTextRef.value) {
    typewriterTextRef.value.textContent = "";
    let i = 0;

    const typeWriter = () => {
      if (i < text.length) {
        typewriterTextRef.value.textContent += text.charAt(i);
        i++;
        requestAnimationFrame(typeWriter);
      }
    };

    typeWriter();
  }
};

const beforeEnter = (el) => {
  el.style.opacity = 0;
  el.style.transform = "scale(0.8)";
};

const enter = (el, done) => {
  el.style.opacity = 1;
  el.style.transform = "scale(1)";
  el.addEventListener("transitionend", done);
};

const leave = (el, done) => {
  el.style.opacity = 0;
  el.style.transform = "scale(0.8)";
  el.addEventListener("transitionend", done);
};

const showDetails = (product) => {
  selectedProduct.value = product;
};

const closeDetails = () => {
  selectedProduct.value = null;
};
</script>
<style scoped>
.container {
  max-width: 900px;
  margin: 0 auto;
  padding: 20px;
  text-align: center;
  font-family: "Roboto", sans-serif;
  color: #ffffff; /* Contrasting color for text */
}

.title {
  font-size: 28px;
  margin-bottom: 20px;
  font-weight: 700;
  color: #ffffff; /* Contrasting color for text */
}

.input {
  padding: 12px;
  font-size: 16px;
  border: 1px solid #cccccc;
  border-radius: 4px;
  flex: 1;
  margin-right: 10px;
  transition: border-color 0.3s ease;
  color: #333333; /* Contrasting color for text */
}

.input:focus {
  outline: none;
  border-color: #fbbf24; /* Contrasting color for border */
}

.button {
  padding: 12px 20px;
  font-size: 16px;
  background-color: #fbbf24; /* Contrasting color for background */
  color: #065f46; /* emerald-800 */
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  position: relative;
}

.button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.spinner {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid #065f46; /* emerald-800 */
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.response-container {
  background-color: #f2f2f2;
  padding: 20px;
  border-radius: 4px;
  margin-top: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  opacity: 0;
  transform: translateY(20px);
  animation: fadeIn 0.5s ease forwards;
}

.response-header {
  margin-bottom: 10px;
}

.product-list {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

.product-item {
  background-color: #d3e0db; /* Contrasting color for background */
  padding: 10px;
  border-radius: 4px;
  margin: 5px;
  transition: transform 0.3s ease, opacity 0.3s ease;
  cursor: pointer;
  color: #065f46; /* emerald-800 */
}

.product-item:hover {
  background-color: #c1d9ce; /* Contrasting color for background on hover */
  transform: scale(1.05);
}

.product-details {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #ffffff;
  padding: 20px;
  border-radius: 4px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 90%;
  z-index: 10;
  animation: fadeIn 0.3s ease;
}

.product-details-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.close-button {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #065f46; /* emerald-800 */
}

.product-details-footer {
  margin-top: 20px;
  text-align: right;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

@keyframes fadeIn {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}
</style>
