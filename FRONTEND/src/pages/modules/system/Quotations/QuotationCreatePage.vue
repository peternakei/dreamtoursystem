<script setup lang="ts">
import {onMounted,ref} from 'vue'
import {useRoute,useRouter} from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import Field from '@/components/services/ServiceField.vue'
import {Button} from '@/components/ui/button'
import {formError} from '@/components/services/formErrors'
import api from '@/axiosClient'
const route=useRoute(),router=useRouter(),catalog=ref<any>(null),form=ref<any>({}),busy=ref(false),error=ref('')
onMounted(async()=>{try{catalog.value=(await api.get('/workspace/inquiries/'+route.params.id+'/quotation-options')).data;form.value={title:catalog.value.inquiry.tour_title||'Travel proposal',trip_id:'',currency_id:catalog.value.defaultCurrencyId,service_class_id:catalog.value.inquiry.service_class_id||'',subtitle:'',introduction:'',internal_notes:''}}catch(e){error.value=formError(e)}})
async function save(){busy.value=true;error.value='';try{const {data}=await api.post('/workspace/inquiries/'+route.params.id+'/quotations',form.value);await router.push('/quotation-versions/'+data.version_uuid+'/builder')}catch(e){error.value=formError(e)}finally{busy.value=false}}
</script>
<template><MainLayout title="Create quotation"><div class="space-y-5"><RouterLink :to="'/inquiries/'+route.params.id+'/details'" class="underline">Back to inquiry</RouterLink><h2 class="text-xl font-semibold">Create trip quotation</h2><p v-if="error" role="alert" class="text-destructive">{{error}}</p><form v-if="catalog" class="space-y-4 rounded-xl border bg-card p-5 text-card-foreground" @submit.prevent="save"><div class="grid gap-4 md:grid-cols-2"><Field v-model="form.title" label="Title" required/><Field v-model="form.subtitle" label="Subtitle"/><Field v-model="form.trip_id" label="Start from trip" type="select" :options="[{value:'',label:'Tailor-made'},...catalog.trips.map((t:any)=>({value:t.uuid,label:t.name}))]"/><Field v-model="form.currency_id" label="Currency" type="select" :options="catalog.currencies.map((c:any)=>({value:c.id,label:c.short_name}))"/><Field v-model="form.service_class_id" label="Service class" type="select" :options="[{value:'',label:'Not specified'},...catalog.serviceClasses.map((s:any)=>({value:s.id,label:s.name}))]"/></div><Field v-model="form.introduction" label="Introduction" type="textarea"/><Field v-model="form.internal_notes" label="Private notes" type="textarea"/><Button type="submit" :disabled="busy">{{busy?'Creating…':'Create and open builder'}}</Button></form></div></MainLayout></template>
