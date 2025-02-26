<script lang="ts" setup>
  const props = defineProps<{
    type: 'button' | 'link'
    href: string | undefined
    target: '_blank' | '_self' | undefined
  }>()
</script>

<template>
  <div class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded font-semibold">
    <div v-if="type === 'button'" type="button">
      <slot />
    </div>

    <div>
      <a v-if="type === 'link'" :href="href" :target="target">
        <slot />
      </a>
    </div>
  </div>
</template>
