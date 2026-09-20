<script setup lang="ts">
import {ref} from 'vue'
import Field from './ServiceField.vue'
const props=defineProps<{modelValue?:Record<string,any>|null,fields:string[]}>();const emit=defineEmits(['update:modelValue']);const locale=ref('fr')
function set(field:string,value:any){emit('update:modelValue',{...props.modelValue,[locale.value]:{...props.modelValue?.[locale.value],[field]:value}})}
</script>
<template><details class="rounded-lg border p-3"><summary class="cursor-pointer font-medium">French and Swahili content</summary><p class="my-2 text-xs text-muted-foreground">Blank translations use the English content.</p><Field v-model="locale" label="Language" type="select" :options="[{value:'fr',label:'French'},{value:'sw',label:'Swahili'}]"/><div class="mt-3 grid gap-3 sm:grid-cols-2"><Field v-for="field in fields" :key="field" :label="field.replaceAll('_',' ')" :model-value="modelValue?.[locale]?.[field]" :type="['description','text','seo_description'].includes(field)?'textarea':'text'" @update:model-value="set(field,$event)"/></div></details></template>
