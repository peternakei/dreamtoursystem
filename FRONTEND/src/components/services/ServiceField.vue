<script setup lang="ts">
import {useId} from 'vue'
const props=withDefaults(defineProps<{label:string,modelValue?:any,type?:string,options?:{value:any,label:string}[],required?:boolean,disabled?:boolean,min?:number,step?:string}>(),{type:'text'})
const emit=defineEmits(['update:modelValue']);const id=useId()
function update(event:Event){const target=event.target as HTMLInputElement;emit('update:modelValue',props.type==='checkbox'?target.checked:props.type==='number'?(target.value===''?null:Number(target.value)):target.value)}
</script>
<template><div class="min-w-0 space-y-1"><label :for="id" class="block text-sm font-medium">{{label}}<span v-if="required" aria-hidden="true"> *</span></label>
<textarea v-if="type==='textarea'" :id="id" :value="modelValue" :required="required" :disabled="disabled" rows="3" class="w-full rounded-md border bg-card p-2 text-card-foreground" @input="update"/>
<select v-else-if="type==='select'" :id="id" :value="modelValue" :required="required" :disabled="disabled" class="w-full rounded-md border bg-card p-2 text-card-foreground" @change="update"><option v-for="o in options" :key="String(o.value)" :value="o.value">{{o.label}}</option></select>
<input v-else :id="id" :type="type" :value="modelValue" :checked="type==='checkbox'?!!modelValue:undefined" :required="required" :disabled="disabled" :min="min" :step="step" :class="type==='checkbox'?'h-5 w-5 accent-primary':'w-full rounded-md border bg-card p-2 text-card-foreground'" @input="update"/>
</div></template>
