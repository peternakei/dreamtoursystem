<script setup lang="ts">
import {useId,computed} from 'vue'
import type {Field} from './types'
import RichTextField from './RichTextField.vue'
import FilePicker from '@/components/services/FilePicker.vue'
const props=defineProps<{field:Field;modelValue:any;busy?:boolean}>(),emit=defineEmits(['update:modelValue']),id=useId()
const model=computed({get:()=>props.modelValue,set:value=>emit('update:modelValue',value)})
function input(event:Event){const e=event.target as HTMLInputElement;emit('update:modelValue',props.field.type==='checkbox'?e.checked:e.value)}
</script>
<template><div :class="{'hidden':field.type==='hidden'}" class="min-w-0 space-y-1"><label v-if="field.type!=='hidden'" :for="id" class="block text-sm font-medium">{{field.label}}<span v-if="field.required"> *</span></label>
<RichTextField v-if="field.type==='richtext'" :model-value="modelValue||''" :disabled="busy||field.disabled" :label="field.label" @update:model-value="emit('update:modelValue',$event)"/>
<select v-else-if="field.type==='select'" :id="id" v-model="model" :multiple="field.multiple" :required="field.required" :disabled="field.disabled||busy" class="field"><option v-for="o in field.options" :key="o.value" :value="o.value">{{o.label}}</option></select>
<textarea v-else-if="field.type==='textarea'" :id="id" :value="modelValue" :required="field.required" :disabled="field.disabled||busy" rows="4" class="field" @input="input"/>
<FilePicker v-else-if="field.type==='file'" :id="id" v-model="model" :label="field.label" :multiple="field.multiple" :required="field.required" :disabled="busy||field.disabled"/>
<input v-else-if="field.type==='checkbox'" :id="id" type="checkbox" :checked="!!modelValue" :disabled="busy||field.disabled" @change="input"/>
<input v-else :id="id" :type="field.type" :value="modelValue" :required="field.required" :disabled="field.disabled||busy" :min="field.min||undefined" :max="field.max||undefined" :step="field.step||undefined" :maxlength="field.maxlength?Number(field.maxlength):undefined" :placeholder="field.placeholder" class="field" @input="input"/>
</div></template>
<style scoped>.field{width:100%;border:1px solid var(--input);border-radius:var(--radius);background:var(--background);padding:.55rem .75rem;font-size:.875rem}.field:focus{outline:2px solid var(--ring);outline-offset:2px}select[multiple]{min-height:9rem}</style>
