<script setup lang="ts">
import { computed, ref, watch, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import DataTableReport from '@/components/datatable/DataTableReport.vue'
import type { Column, TableRow } from '@/components/datatable/types'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { api, errorMessage } from '@/axiosClient'

const props = defineProps<{ type: string }>()
const route = useRoute(), router = useRouter()
const titles: Record<string, string> = { activity: 'Activity logs', requests: 'Request logs', errors: 'Error logs' }
const rows = ref<TableRow[]>([]), loading = ref(false), error = ref('')
const page = ref(1), lastPage = ref(1), total = ref(0), perPage = ref(25)
const search = ref(''), startDate = ref(''), endDate = ref(''), userId = ref('')
const selected = ref<TableRow | null>(null), detailsLoading = ref(false)
const requestId = computed(() => typeof route.query.request_id === 'string' ? route.query.request_id : '')
const invalidDates = computed(() => !!startDate.value && !!endDate.value && startDate.value > endDate.value)
const timezone = ref('Africa/Dar_es_Salaam')
let generation = 0, detailGeneration = 0
const columns = computed<Column[]>(() => [
  { key: 'occurred_at', label: 'Date & time' },
  { key: 'user_name', label: 'User' },
  ...(props.type === 'requests' ? [
    { key: 'method', label: 'Method' }, { key: 'url', label: 'Route' },
    { key: 'response_status', label: 'Status' }, { key: 'duration', label: 'Time (ms)' },
  ] : [{ key: 'module', label: 'Module' }, { key: 'action', label: 'Action' }, { key: 'message', label: 'Description' }]),
  { key: 'details', label: 'Details' },
])
async function load(reset = false) {
  if (invalidDates.value) return
  if (reset) page.value = 1
  const current = ++generation
  loading.value = true; error.value = ''; selected.value = null; ++detailGeneration; detailsLoading.value = false
  try {
    const { data } = await api.get('/workspace/logs/' + props.type, { params: {
      page: page.value, per_page: perPage.value, search: search.value || undefined,
      start_date: startDate.value || undefined, end_date: endDate.value || undefined,
      user_id: userId.value || undefined, request_id: requestId.value || undefined,
    } })
    if (current !== generation) return
    rows.value = data.data; page.value = data.current_page; lastPage.value = data.last_page; total.value = data.total
    if (data.timezone) timezone.value = data.timezone
  } catch (e) {
    if (current === generation) { error.value = errorMessage(e); rows.value = []; total.value = 0 }
  } finally { if (current === generation) loading.value = false }
}
async function details(row: TableRow) {
  const current = ++detailGeneration
  detailsLoading.value = true; error.value = ''; selected.value = null
  try {
    const { data } = await api.get('/workspace/logs/' + props.type + '/' + row.uuid)
    if (current === detailGeneration) selected.value = data.data
  } catch (e) { if (current === detailGeneration) error.value = errorMessage(e) }
  finally { if (current === detailGeneration) detailsLoading.value = false }
}
function reset() { search.value = ''; startDate.value = ''; endDate.value = ''; userId.value = ''; void load(true) }
function goPage(next: number) { page.value = next; void load() }
function closeDetails() { selected.value = null; ++detailGeneration; detailsLoading.value = false }
watch(() => [props.type, requestId.value], () => { reset() }, { immediate: true })
onBeforeUnmount(() => { ++generation; ++detailGeneration })
</script>

<template>
  <MainLayout :title="titles[type] || 'System logs'">
    <div class="space-y-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h2 class="text-xl font-semibold">{{ titles[type] }}</h2><p class="text-sm text-muted-foreground">Audit history · Dates use {{ timezone }} time</p></div>
        <Button variant="outline" :disabled="loading" @click="load()">Refresh</Button>
      </div>
      <nav aria-label="Log categories" class="flex flex-wrap gap-2">
        <RouterLink v-for="(title, key) in titles" :key="key" :to="{ path: '/logs/' + key + '/list', query: requestId ? { request_id: requestId } : {} }"
          class="rounded-md border px-4 py-2 text-sm" :class="type === key ? 'bg-primary text-primary-foreground' : 'bg-card text-card-foreground'" :aria-current="type === key ? 'page' : undefined">{{ title }}</RouterLink>
      </nav>
      <div v-if="requestId" class="flex flex-wrap items-center gap-3 rounded-md border p-3 text-sm">
        <span class="break-all">Request: {{ requestId }}</span><Button size="sm" variant="outline" @click="router.replace({ query: {} })">Clear request filter</Button>
      </div>
      <form class="flex flex-wrap items-end gap-3 rounded-xl border bg-card text-card-foreground p-4" @submit.prevent="load(true)">
        <div class="space-y-1"><label for="logs-start" class="text-sm">Start date</label><Input id="logs-start" v-model="startDate" type="date" /></div>
        <div class="space-y-1"><label for="logs-end" class="text-sm">End date</label><Input id="logs-end" v-model="endDate" type="date" :min="startDate || undefined" /></div>
        <div class="min-w-40 flex-1 space-y-1"><label for="logs-search" class="text-sm">Search logs</label><Input id="logs-search" v-model="search" maxlength="150" placeholder="Module, action, route or request ID" type="search" /></div>
        <div class="w-28 space-y-1"><label for="logs-user" class="text-sm">User ID</label><Input id="logs-user" v-model="userId" type="number" min="1" placeholder="All users" /></div>
        <Button type="submit" :disabled="loading || invalidDates">Apply filters</Button>
        <Button type="button" variant="outline" :disabled="loading" @click="reset">Reset</Button>
        <p v-if="invalidDates" role="alert" class="w-full text-sm text-destructive">End date must be on or after start date.</p>
      </form>
      <p v-if="error" role="alert" class="rounded-md border border-destructive p-3 text-destructive">{{ error }}</p>
      <DataTableReport :columns="columns" :rows="rows" :loading="loading" row-key="uuid" :show-date-filter="false" :searchable="false" :paginated="false" :numbered="false">
        <template #cell-details="{ row }"><Button size="sm" variant="outline" :disabled="detailsLoading" @click="details(row)">View</Button></template>
        <template #cell-occurred_at="{ value }"><span class="whitespace-nowrap">{{ String(value).slice(0, 19) }}</span></template>
      </DataTableReport>
      <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
        <div class="flex items-center gap-2"><label for="logs-size">Rows per page</label><select id="logs-size" v-model.number="perPage" :disabled="loading" class="rounded-md border bg-card text-card-foreground p-2" @change="load(true)"><option v-for="size in [25, 50, 100]" :key="size" :value="size">{{ size }}</option></select><span aria-live="polite">{{ total }} records</span></div>
        <div class="flex items-center gap-3"><Button variant="outline" size="sm" :disabled="loading || page <= 1" @click="goPage(page - 1)">Previous</Button><span>Page {{ page }} of {{ lastPage }}</span><Button variant="outline" size="sm" :disabled="loading || page >= lastPage" @click="goPage(page + 1)">Next</Button></div>
      </div>
      <p v-if="detailsLoading" role="status">Loading details…</p>
      <section v-if="selected" aria-label="Log details" class="space-y-4 rounded-xl border bg-card text-card-foreground p-5" tabindex="0">
        <div class="flex items-center justify-between"><h3 class="font-semibold">Log details</h3><Button variant="outline" size="sm" @click="closeDetails">Close details</Button></div>
        <p class="text-sm">{{ selected.user_name }} · {{ selected.occurred_at }}</p>
        <div v-if="selected.request_id" class="flex flex-wrap gap-2">
          <RouterLink v-for="(title, key) in titles" :key="key" :to="{ path: '/logs/' + key + '/list', query: { request_id: String(selected.request_id) } }" class="text-sm text-card-foreground underline">Related {{ title.toLowerCase() }}</RouterLink>
        </div>
        <dl class="space-y-3"><div v-for="(value, key) in selected" :key="key"><dt class="text-xs font-semibold uppercase text-muted-foreground">{{ String(key).replaceAll('_', ' ') }}</dt><dd><pre class="mt-1 max-h-80 overflow-auto whitespace-pre-wrap break-all rounded-md bg-muted p-3 text-xs">{{ typeof value === 'object' ? JSON.stringify(value, null, 2) : value ?? '—' }}</pre></dd></div></dl>
      </section>
    </div>
  </MainLayout>
</template>
