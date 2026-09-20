<script setup lang="ts">
import {reactive,ref,watch} from 'vue'
import {useRouter} from 'vue-router'
import {Button} from '@/components/ui/button'
import {LoaderCircle} from 'lucide-vue-next'
import api,{errorMessage} from '@/axiosClient'
import type {Form,Field} from './types'
import WorkspaceField from './WorkspaceField.vue'
const router=useRouter()
const props=defineProps<{form:Form}>(),emit=defineEmits<{saved:[]}>()
const values=reactive<Record<string,any>>({}),rows=ref<Record<string,any>[][]>([]),busy=ref(false),error=ref(''),errors=ref<Record<string,string[]>>({})
const initial=(fields:Field[])=>Object.fromEntries(fields.map(f=>[f.name,f.type==='file'?null:JSON.parse(JSON.stringify(f.value??null))]))
watch(()=>props.form,form=>{Object.keys(values).forEach(k=>delete values[k]);Object.assign(values,initial(form.fields));rows.value=(form.repeaters||[]).map(r=>[initial(r.fields)]);error.value='';errors.value={}},{immediate:true})
function append(body:FormData,field:Field,value:any){if(field.disabled||value===null||value===undefined)return;if(field.type==='checkbox'){body.append(field.name,value?'1':'0');return}if(Array.isArray(value)){for(const item of value)body.append(field.name.endsWith('[]')?field.name:field.name+'[]',item)}else body.append(field.name,value)}
async function submit(){busy.value=true;error.value='';errors.value={};try{
 const body=new FormData();if(props.form.method!=='POST')body.append('_method',props.form.method)
 for(const f of props.form.fields)append(body,f,values[f.name]);(props.form.repeaters||[]).forEach((group,i)=>rows.value[i].forEach((row,index)=>group.fields.forEach(f=>append(body,{...f,name:f.name.replace(/\[\]$/, '['+index+']')},row[f.name]))))
 const {data}=await api.post(props.form.action,body,{headers:{'X-Safari-Workspace':'1'}})
 if(data?.status===false)throw new Error(data.message||'The action could not be completed.')
 if(data?.workspace_path && /^\/quotation-versions\/[a-f0-9-]{36}\/builder$/i.test(data.workspace_path)) await router.push(data.workspace_path); else emit('saved')
 }catch(e:any){error.value=errorMessage(e);errors.value=e.response?.data?.errors||{}}finally{busy.value=false}}
</script>
<template><form @submit.prevent="submit" class="space-y-4"><p v-if="error" role="alert" class="rounded-md bg-destructive/10 p-3 text-sm text-destructive">{{error}}</p>
<div v-for="(field,index) in form.fields" :key="field.name+index"><WorkspaceField v-model="values[field.name]" :field="field" :busy="busy"/><p v-if="errors[field.name]" class="text-xs text-destructive">{{errors[field.name].join(' ')}}</p></div>
<section v-for="(group,g) in form.repeaters||[]" :key="g" class="space-y-3 rounded border p-3"><h4 class="font-semibold">{{group.label}}</h4><div v-for="(row,r) in rows[g]" :key="r" class="space-y-3 rounded border p-3"><WorkspaceField v-for="f in group.fields" :key="f.name" v-model="row[f.name]" :field="f" :busy="busy"/><Button type="button" size="sm" variant="outline" :disabled="busy||rows[g].length===1" @click="rows[g].splice(r,1)">Remove item</Button></div><Button type="button" variant="outline" :disabled="busy" @click="rows[g].push(initial(group.fields))">Add item</Button></section>
<div class="flex justify-end border-t pt-4"><Button type="submit" :disabled="busy" :variant="form.method==='DELETE'?'destructive':'default'"><LoaderCircle v-if="busy" class="h-4 w-4 animate-spin"/>{{busy?'Saving…':form.title}}</Button></div></form></template>
