<script setup lang="ts">
import {computed, ref, watch} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {Plus, RefreshCw, ArrowLeft, ExternalLink} from 'lucide-vue-next'
import MainLayout from '@/layouts/MainLayout.vue'
import {Button} from '@/components/ui/button'
import DataTableReport from '@/components/datatable/DataTableReport.vue'
import type {Column} from '@/components/datatable/types'
import {Card, CardContent} from '@/components/ui/card'
import {Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription} from '@/components/ui/dialog'
import EntityForm from './EntityForm.vue'
import type {Page, Form} from './types'
import api, {errorMessage} from '@/axiosClient'
import {toast} from 'vue-sonner'

const backendUrl = import.meta.env.VITE_BACKEND_URL || 'http://127.0.0.1:8001'
const props = withDefaults(defineProps<{ module: string; details?: boolean; dateField?: string; showDateFilter?: boolean }>(), { dateField: '', showDateFilter: true }), route = useRoute(), router = useRouter()
const data = ref<Page | null>(null), loading = ref(true), error = ref(''),
    selected = ref<Form | null>(null), open = ref(false)
const title = computed(() => data.value?.module.title || props.module.replaceAll('_', ' '))
const fields = computed(() => {
  const row = data.value?.records[0];
  if (!row) return [];
  const preferred = ['name', 'username', 'reference', 'quotation_number', 'booking_number', 'invoice_number', 'email', 'phone', 'title', 'status', 'is_active', 'created_at'];
  const keys = preferred.filter(k => k in row);
  return keys.length ? keys.slice(0, 5) : Object.keys(row).filter(k => !['id', 'uuid', 'deleted_at', 'created_by', 'updated_by', 'updated_at'].includes(k) && !k.endsWith('_id')).slice(0, 5)
})
const columns = computed<Column[]>(() => [
  ...fields.value.map(key => ({key, label: label(key), sortable: true, formatter: display})),
  {key: 'actions', label: 'Actions', align: 'right'},
])
const pageForms = computed(() => (data.value?.forms || []).filter(form => props.details || !form.recordKey))
const detailEntries = computed(() => Object.values(data.value?.details || {})[0] || {})
const fullPath = computed(() => {
  const path = data.value?.module.showPath;
  return props.details && path ? path.replace(/\{[^}]+\}/, String(route.params.id)) : '/' + props.module
})

function label(key: string) {
  return key.replaceAll('_', ' ').replace(/\b\w/g, s => s.toUpperCase())
}

function display(value: any): string {
  if (value === null || value === undefined || value === '') return '—';
  if (typeof value === 'boolean') return value ? 'Yes' : 'No';
  if (typeof value === 'object') return value.name || value.title || value.reference || JSON.stringify(value);
  return String(value)
}

async function load() {
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
      <div class="flex flex-wrap gap-2">
        <Button variant="outline" size="sm" @click="load" :disabled="loading">
          <RefreshCw class="h-4 w-4"/>
          Refresh
        </Button>
        <a :href="backendUrl+fullPath" target="_blank" rel="noopener">
          <Button variant="outline" size="sm">
            <ExternalLink class="h-4 w-4"/>
            Full management
          </Button>
        </a>
        <Button v-for="(form,i) in pageForms" :key="i" size="sm"
                :variant="form.method==='DELETE'?'destructive':'default'" @click="choose(form)">
          <Plus v-if="!details" class="h-4 w-4"/>
          {{ form.title }}
        </Button>
      </div>
    </div>
    <p v-if="error" role="alert" class="mb-4 rounded-md bg-destructive/10 p-4 text-sm text-destructive">{{ error }}</p>
    <DataTableReport v-if="!details" :key="module"
      :columns="columns" :rows="data?.records || []" :loading="loading"
      :date-field="dateField" :show-date-filter="showDateFilter" :search-keys="fields"
    >
      <template #cell-actions="{ row }">
        <div class="flex items-center justify-end gap-1">
          <RouterLink v-if="data?.module.showPath" :to="'/'+module+'/'+(row.uuid||row.id)+'/details'">
            <Button variant="outline" size="sm">View</Button>
          </RouterLink>
          <Button v-for="(form,fi) in (data?.forms||[]).filter(f=>f.recordKey===row.uuid)" :key="fi"
            variant="ghost" size="sm" @click="choose(form)">{{ form.title }}</Button>
        </div>
      </template>
    </DataTableReport>
    <Card v-else>
      <CardContent class="p-0">
        <p v-if="loading" class="p-8 text-center text-muted-foreground">Loading…</p>
        <dl v-else class="grid gap-5 p-6 md:grid-cols-2">
          <template v-for="(value,key) in detailEntries" :key="key">
            <div v-if="!['password','remember_token','deleted_at'].includes(key) && typeof value!=='object'">
              <dt class="text-xs font-medium uppercase tracking-wide text-muted-foreground">{{ label(key) }}</dt>
              <dd class="mt-1 whitespace-pre-wrap break-words text-sm">{{ display(value) }}</dd>
            </div>
          </template>
        </dl>
      </CardContent>
    </Card>
    <Dialog v-model:open="open">
      <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>{{ selected?.title }}</DialogTitle>
          <DialogDescription>Review the details below, then save your changes.</DialogDescription>
        </DialogHeader>
        <EntityForm v-if="selected" :form="selected" @saved="saved"/>
      </DialogContent>
    </Dialog>
  </MainLayout>
</template>
