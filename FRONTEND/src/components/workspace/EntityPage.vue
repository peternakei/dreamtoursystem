<script setup lang="ts">
import {computed, ref, watch} from 'vue'
import {useRoute, useRouter, onBeforeRouteLeave, onBeforeRouteUpdate} from 'vue-router'
import {Plus, RefreshCw, ArrowLeft} from 'lucide-vue-next'
import MainLayout from '@/layouts/MainLayout.vue'
import {Button} from '@/components/ui/button'
import DataTableReport from '@/components/datatable/DataTableReport.vue'
import {listPresentation} from './listPresentation'
import type {Column} from '@/components/datatable/types'
import {Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription} from '@/components/ui/dialog'
import EntityForm from './EntityForm.vue'
import RecordDetails from './RecordDetails.vue'
import LibraryMedia from '@/components/services/LibraryMedia.vue'
import TripPlanner from './TripPlanner.vue'
import {mediaUrl} from '@/components/services/mediaUrl'
import type {Page, Form} from './types'
import api, {errorMessage} from '@/axiosClient'
import {toast} from 'vue-sonner'

const props = withDefaults(defineProps<{
  module: string;
  details?: boolean;
  dateField?: string;
  showDateFilter?: boolean
}>(), {dateField: '', showDateFilter: true}), route = useRoute(), router = useRouter()
const data = ref<Page | null>(null), loading = ref(true), error = ref(''),
    selected = ref<Form | null>(null), open = ref(false)
const plannerDirty = ref(false)
function discardPlanner() { if(plannerDirty.value && !window.confirm('Discard unsaved itinerary changes?')) return false; plannerDirty.value=false; return true }
onBeforeRouteLeave(discardPlanner)
onBeforeRouteUpdate(discardPlanner)
const title = computed(() => data.value?.module.title || props.module.replaceAll('_', ' '))
const list = computed(() => listPresentation(props.module, data.value?.records || []))
const fields = computed(() => list.value.columns.map(column => column.key))
const columns = computed<Column[]>(() => [
  ...list.value.columns,
  {key: 'actions', label: 'Actions', align: 'right'},
])
const pageForms = computed(() => (data.value?.forms || []).filter(form => props.details || !form.recordKey))
const rowForms = (row: Record<string,unknown>) => (Array.isArray(row._forms) ? row._forms : []) as Form[]
const sections = computed(() => (data.value?.sections || []).map(section => (section.columns ? {...section,columns:section.columns} : {...section, ...listPresentation(section.module, section.rows)})))
const detailEntries = computed(() => Object.values(data.value?.details || {})[0] || {})

async function load() {
  if(!discardPlanner()) return;
  loading.value = true;
  error.value = '';
  try {
    data.value = (await api.get('/workspace/modules/' + props.module + (props.details ? '/' + route.params.id : ''))).data
  } catch (e) {
    error.value = errorMessage(e)
  } finally {
    loading.value = false
  }
}

function choose(form: Form) {
  selected.value = form;
  open.value = true
}

async function saved() {
  open.value = false;
  toast.success('Changes saved');
  if (selected.value?.method === 'DELETE' && props.details && selected.value.action === '/'+props.module+'/'+route.params.id) { await router.push('/'+props.module+'/list'); return }
  await load()
}

watch(() => [props.module, route.params.id], () => {
  load()
}, {immediate: true});
</script>
<template>
  <MainLayout :title="title">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <Button v-if="details" variant="outline" size="icon" @click="router.push('/'+module+'/list')"
                aria-label="Back to list">
          <ArrowLeft class="h-4 w-4"/>
        </Button>
        <div>
          <h2 class="text-xl font-semibold capitalize">{{ details ? 'Details' : title }}</h2>
          <p class="mt-1 text-sm text-muted-foreground">
            {{ details ? 'Manage this record' : (data?.records.length || 0) + ' records' }}</p>
        </div>
      </div>
      <div class="flex max-w-full flex-wrap gap-2">
        <Button variant="outline" size="sm" @click="load" :disabled="loading">
          <RefreshCw class="h-4 w-4"/>
          Refresh
        </Button>
        <Button v-for="(form,i) in pageForms" :key="i" size="sm" class="h-auto max-w-full whitespace-normal py-2 text-left"
                :variant="form.method==='DELETE'?'destructive':'default'" @click="choose(form)">
          <Plus v-if="!details" class="h-4 w-4"/>
          {{ form.title }}
        </Button>
      </div>
    </div>
    <p v-if="error" role="alert" class="mb-4 rounded-md bg-destructive/10 p-4 text-sm text-destructive">{{ error }}</p>
    <DataTableReport v-if="!details" :key="module"
                     :columns="columns" :rows="list.rows" :loading="loading"
                     :date-field="dateField" :show-date-filter="showDateFilter" :search-keys="fields"
    >
      <template #cell-actions="{ row }">
        <div class="flex items-center justify-end gap-1">
          <RouterLink v-if="data?.module.showPath" :to="'/'+module+'/'+(row.uuid||row.id)+(module==='inquiries' && row.service_details_exists ? '/services' : '/details')">
            <Button variant="outline" size="sm">View</Button>
          </RouterLink>
          <Button v-for="(form,fi) in (data?.forms||[]).filter(f=>f.recordKey===row.uuid)" :key="fi"
                  variant="ghost" size="sm" @click="choose(form)">{{ form.title }}
          </Button>
        </div>
      </template>
    </DataTableReport>
<template v-else-if="!error">
      <p v-if="loading" role="status" class="p-8 text-center text-muted-foreground">Loading…</p>
      <template v-else>
        <RecordDetails :record="detailEntries" :module="module"/>
        <div v-if="data?.links?.length" class="my-5 flex flex-wrap gap-2">
          <RouterLink v-for="link in data.links" :key="link.to" :to="link.to"><Button variant="outline">{{link.label}}</Button></RouterLink>
        </div>
        <p v-for="notice in data?.notices" :key="notice" class="my-4 text-sm text-muted-foreground">{{notice}}</p>
        <section v-for="section in sections" :key="section.title" class="mt-6 space-y-3">
          <h3 class="text-lg font-semibold">{{section.title}}</h3>
          <DataTableReport :rows="section.rows" :columns="[...(section.columns||[]),{key:'actions',label:'Actions'}]" :show-date-filter="false">
            <template #cell-actions="{row}"><RouterLink v-if="section.module" :to="'/'+section.module+'/'+(row.uuid||row.id)+(section.module==='inquiries' && row.service_details_exists ? '/services' : '/details')"><Button variant="outline" size="sm">View</Button></RouterLink><Button v-for="form in rowForms(row)" :key="form.action" variant="outline" size="sm" class="ml-2" @click="choose(form)">{{form.title}}</Button></template>
          </DataTableReport>
        </section>
        <section v-if="data?.documents?.length" class="mt-6 grid gap-3 sm:grid-cols-2"><figure v-for="doc in data.documents" :key="doc.url" class="rounded border bg-card p-3"><img :src="mediaUrl(doc.url)" :alt="doc.title" class="h-56 w-full object-contain"/><figcaption class="mt-2 text-sm">{{doc.title}}</figcaption></figure></section>
        <LibraryMedia v-if="data?.media" :type="data.media.type" :uuid="data.media.uuid" :images="data.media.images" class="mt-6" @saved="load"/>
        <details v-if="data?.planner" :open="route.query.tab==='itinerary'" class="mt-6 rounded-lg border p-4"><summary class="cursor-pointer font-semibold">Itinerary editor</summary><TripPlanner class="mt-4" :planner="data.planner" @dirty="plannerDirty=$event" @saved="load"/></details>
      </template>
    </template>
    <RouterLink v-if="module==='inquiries' && details && detailEntries.service_details_exists" :to="'/inquiries/'+route.params.id+'/services'" class="mt-4 inline-block"><Button variant="outline">Service request details</Button></RouterLink>
    <Dialog v-model:open="open">
      <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>{{ selected?.title }}</DialogTitle>
          <DialogDescription>{{selected?.description || 'Review the details below, then save your changes.'}}</DialogDescription>
        </DialogHeader>
        <EntityForm v-if="selected" :form="selected" @saved="saved"/>
      </DialogContent>
    </Dialog>
  </MainLayout>
</template>
