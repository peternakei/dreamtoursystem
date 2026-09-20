<script setup lang="ts">
import {reactive,ref,watch} from 'vue'
import {Button} from '@/components/ui/button'
import {LoaderCircle} from 'lucide-vue-next'
import api,{errorMessage} from '@/axiosClient'
import type {Form} from './types'
const props=defineProps<{form:Form}>(),emit=defineEmits<{saved:[]}>()
const values=reactive<Record<string,any>>({}),busy=ref(false),error=ref(''),errors=ref<Record<string,string[]>>({})
watch(()=>props.form,form=>{Object.keys(values).forEach(k=>delete values[k]);form.fields.forEach(f=>values[f.name]=f.type==='file'?null:f.value);error.value='';errors.value={}},{immediate:true})
function files(event:Event,name:string,multiple:boolean){const input=event.target as HTMLInputElement;values[name]=multiple?Array.from(input.files||[]):input.files?.[0]}
async function submit(){busy.value=true;error.value='';errors.value={};try{
 const body=new FormData();if(props.form.method!=='POST')body.append('_method',props.form.method)
 for(const field of props.form.fields){if(field.disabled)continue;const v=values[field.name];if(v===null||v===undefined)continue;if(field.type==='checkbox'){if(v)body.append(field.name,'1');continue}if(Array.isArray(v)){for(const item of v)body.append(field.name.endsWith('[]')?field.name:field.name+'[]',item)}else body.append(field.name,v)}
 const {data}=await api.post(props.form.action,body,{headers:{'X-Safari-Workspace':'1'}})
 if(data?.status===false)throw new Error(data.message||'The action could not be completed.')
 emit('saved')
 }catch(e:any){error.value=errorMessage(e);errors.value=e.response?.data?.errors||{}}finally{busy.value=false}}
</script>
<template>
<form @submit.prevent="submit" class="space-y-4">
<p v-if="error" role="alert" class="rounded-md bg-destructive/10 p-3 text-sm text-destructive">{{error}}</p>
<div v-for="(field,index) in form.fields" :key="field.name+index" :class="{'hidden':field.type==='hidden'}">
<label v-if="field.type!=='hidden'" :for="'field-'+index" class="mb-1.5 block text-sm font-medium">{{field.label}}<span v-if="field.required" class="text-destructive"> *</span>
</label>
<select v-if="field.type==='select'" :id="'field-'+index" v-model="values[field.name]" :multiple="field.multiple" :required="field.required" :disabled="field.disabled||busy" class="field">
<option v-for="option in field.options" :key="option.value" :value="option.value">{{option.label}}</option>
</select>
<textarea v-else-if="field.type==='textarea'" :id="'field-'+index" v-model="values[field.name]" :required="field.required" :disabled="field.disabled||busy" rows="4" class="field" :placeholder="field.placeholder"/>
<input v-else-if="field.type==='file'" :id="'field-'+index" type="file" :multiple="field.multiple" :required="field.required" :disabled="busy" @change="files($event,field.name,field.multiple)" class="field">
<input v-else-if="field.type==='checkbox'" :id="'field-'+index" type="checkbox" v-model="values[field.name]" :disabled="busy">
<input v-else :id="'field-'+index" :type="field.type" v-model="values[field.name]" :required="field.required" :disabled="field.disabled||busy" :min="field.min||undefined" :max="field.max||undefined" :maxlength="field.maxlength?Number(field.maxlength):undefined" :placeholder="field.placeholder" class="field">
<p v-if="errors[field.name]" class="mt-1 text-xs text-destructive">{{errors[field.name].join(' ')}}</p>
</div>
<div class="flex justify-end border-t pt-4">
<Button type="submit" :disabled="busy" :variant="form.method==='DELETE'?'destructive':'default'">
<LoaderCircle v-if="busy" class="h-4 w-4 animate-spin"/>{{busy?'Saving…':form.title}}</Button>
</div>
</form>
</template>
<style scoped>.field{width:100%;border:1px solid var(--input);border-radius:var(--radius);background:var(--background);padding:.55rem .75rem;font-size:.875rem}.field:focus{outline:2px solid var(--ring);outline-offset:2px}</style>
