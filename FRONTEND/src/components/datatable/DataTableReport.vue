<script setup lang="ts">
import { computed, ref, useId, watch } from 'vue'
import { ArrowDown, ArrowUp, ArrowUpDown, Search } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { displayValue, filterRows, sortRows } from './tableUtils'
import type { Column, TableFilters, TableRow } from './types'

const props = withDefaults(defineProps<{
  columns: Column[]
  rows: TableRow[]
  loading?: boolean
  rowKey?: string
  dateField?: string
  showDateFilter?: boolean
  searchable?: boolean
  searchKeys?: string[]
  searchPlaceholder?: string
  numbered?: boolean
  paginated?: boolean
  pageSizeOptions?: number[]
  defaultPageSize?: number
  rowClass?: (row: any) => string
}>(), {
  loading: false, rowKey: '', dateField: '', showDateFilter: true,
  searchable: true, searchPlaceholder: 'Search records…', numbered: true,
  paginated: true, pageSizeOptions: () => [10, 25, 50, 100], defaultPageSize: 25,
})

const emit = defineEmits<{
  'filters-change': [filters: TableFilters]
  'page-change': [page: number]
}>()
const id = useId()
const filters = ref<TableFilters>({ startDate: '', endDate: '', search: '' })
const sort = ref<{ key: string | null; dir: 'asc' | 'desc' }>({ key: null, dir: 'asc' })
const page = ref(1)
const pageSize = ref(Number.isInteger(props.defaultPageSize) && props.defaultPageSize > 0 ? props.defaultPageSize : 25)
const sizes = computed(() => [...new Set([...props.pageSizeOptions, pageSize.value])].filter(n => Number.isInteger(n) && n > 0).sort((a, b) => a - b))
const datesEnabled = computed(() => props.showDateFilter && !!props.dateField)
const invalidRange = computed(() => datesEnabled.value && !!filters.value.startDate && !!filters.value.endDate && filters.value.startDate > filters.value.endDate)
const effectiveFilters = computed<TableFilters>(() => ({
  search: props.searchable ? filters.value.search : '',
  startDate: datesEnabled.value ? filters.value.startDate : '',
  endDate: datesEnabled.value ? filters.value.endDate : '',
}))
const filteredRows = computed(() => sortRows(filterRows(props.rows, effectiveFilters.value, datesEnabled.value ? props.dateField : '', props.searchKeys), sort.value.key, sort.value.dir))
const totalPages = computed(() => Math.max(1, Math.ceil(filteredRows.value.length / pageSize.value)))
const paginatedRows = computed(() => props.paginated ? filteredRows.value.slice((page.value - 1) * pageSize.value, page.value * pageSize.value) : filteredRows.value)
const totalCols = computed(() => Math.max(1, props.columns.length + Number(props.numbered)))

watch(effectiveFilters, value => { page.value = 1; emit('filters-change', { ...value }) })
watch(pageSize, () => { page.value = 1 })
watch(totalPages, total => { page.value = Math.min(page.value, total) })
watch(page, value => emit('page-change', value))
watch(() => [datesEnabled.value, props.dateField], () => { filters.value.startDate = ''; filters.value.endDate = '' })
watch(() => props.searchable, enabled => { if (!enabled) filters.value.search = '' })
watch(() => props.columns, columns => {
  if (!columns.some(column => column.key === sort.value.key && column.sortable)) sort.value = { key: null, dir: 'asc' }
})

function sortBy(key: string) {
  sort.value = { key, dir: sort.value.key === key && sort.value.dir === 'asc' ? 'desc' : 'asc' }
  page.value = 1
}
function resetFilters() {
  filters.value = { startDate: '', endDate: '', search: '' }
  sort.value = { key: null, dir: 'asc' }
  page.value = 1
}
function keyFor(row: TableRow, index: number): string | number {
  const value = props.rowKey ? row[props.rowKey] : row.uuid ?? row.id
  return typeof value === 'string' || typeof value === 'number' ? value : index
}
const alignClass = (align?: string) => align === 'right' ? 'text-right' : align === 'center' ? 'text-center' : 'text-left'
</script>

<template>
  <div class="min-w-0 space-y-4" data-testid="data-table" :aria-busy="loading">
    <div v-if="searchable || datesEnabled" class="bg-card text-card-foreground rounded-xl border p-4 shadow-sm">
      <div class="flex flex-wrap items-end gap-4">
        <div v-if="datesEnabled" class="min-w-0 flex-1 space-y-1 sm:flex-none">
          <label :for="id + '-start'" class="text-xs font-medium text-muted-foreground">Start date</label>
          <Input :id="id + '-start'" v-model="filters.startDate" type="date" :aria-invalid="invalidRange" :aria-describedby="invalidRange ? id + '-date-error' : undefined" class="min-w-0" />
        </div>
        <div v-if="datesEnabled" class="min-w-0 flex-1 space-y-1 sm:flex-none">
          <label :for="id + '-end'" class="text-xs font-medium text-muted-foreground">End date</label>
          <Input :id="id + '-end'" v-model="filters.endDate" type="date" :min="filters.startDate || undefined" :aria-invalid="invalidRange" :aria-describedby="invalidRange ? id + '-date-error' : undefined" class="min-w-0" />
        </div>
        <div v-if="searchable" class="min-w-0 basis-full space-y-1 sm:flex-1 sm:basis-48">
          <label :for="id + '-search'" class="text-xs font-medium text-muted-foreground">Search</label>
          <div class="relative">
            <Search aria-hidden="true" class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
            <Input :id="id + '-search'" v-model="filters.search" type="search" :placeholder="searchPlaceholder" class="pl-9" />
          </div>
        </div>
        <Button type="button" variant="outline" size="sm" @click="resetFilters">Reset</Button>
        <p class="text-xs text-muted-foreground sm:ml-auto" aria-live="polite">{{ loading ? 'Loading…' : `Showing ${filteredRows.length} of ${rows.length} records` }}</p>
      </div>
      <p v-if="invalidRange" :id="id + '-date-error'" role="alert" class="mt-3 text-sm text-destructive">End date must be on or after start date.</p>
    </div>

    <div class="bg-card text-card-foreground overflow-x-auto rounded-xl border shadow-sm" tabindex="0" role="region" aria-label="Records table; scroll horizontally to see all columns">
      <table class="w-full text-left text-sm">
        <thead class="border-b bg-muted/50 text-xs text-muted-foreground">
          <tr>
            <th v-if="numbered" scope="col" class="w-12 px-4 py-3"><span aria-hidden="true">#</span><span class="sr-only">Row number</span></th>
            <th v-for="col in columns" :key="col.key" scope="col" class="px-4 py-3 font-medium" :class="[alignClass(col.align), col.key === 'actions' ? 'sticky right-0 z-10 bg-card shadow-[-2px_0_4px_-2px_rgba(0,0,0,0.15)]' : '']"
              :aria-sort="col.sortable ? (sort.key === col.key ? (sort.dir === 'asc' ? 'ascending' : 'descending') : 'none') : undefined">
              <button v-if="col.sortable" type="button" class="inline-flex items-center gap-1 rounded-sm outline-none focus-visible:ring-2 focus-visible:ring-ring" @click="sortBy(col.key)">
                {{ col.label }}
                <component :is="sort.key === col.key ? (sort.dir === 'asc' ? ArrowUp : ArrowDown) : ArrowUpDown" class="h-3.5 w-3.5" aria-hidden="true" />
              </button>
              <span v-else>{{ col.label }}</span>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading"><td :colspan="totalCols" class="p-10 text-center text-muted-foreground" role="status">Loading…</td></tr>
          <tr v-else-if="!paginatedRows.length"><td :colspan="totalCols" class="p-10 text-center text-muted-foreground">{{ rows.length ? 'No records match your filters.' : 'No records yet.' }}</td></tr>
          <template v-else>
            <tr v-for="(row, index) in paginatedRows" :key="keyFor(row, index)" class="border-b last:border-0 hover:bg-muted/30" :class="rowClass?.(row)">
              <td v-if="numbered" class="px-4 py-3 text-muted-foreground">{{ (paginated ? (page - 1) * pageSize : 0) + index + 1 }}.</td>
              <td v-for="col in columns" :key="col.key" class="px-4 py-3" :class="[alignClass(col.align), col.key === 'actions' ? 'sticky right-0 z-10 bg-card shadow-[-2px_0_4px_-2px_rgba(0,0,0,0.15)]' : '']">
                <slot :name="'cell-' + col.key" :value="row[col.key]" :row="row">{{ col.formatter ? col.formatter(row[col.key], row) : displayValue(row[col.key]) }}</slot>
              </td>
            </tr>
          </template>
        </tbody>
        <tfoot v-if="!loading && $slots.footer"><slot name="footer" :filtered-rows="filteredRows" :all-rows="rows" /></tfoot>
      </table>
    </div>

    <div v-if="!loading && filteredRows.length && paginated" class="flex flex-wrap items-center justify-between gap-3 text-sm">
      <div class="flex items-center gap-2">
        <label :for="id + '-size'">Rows per page</label>
        <select :id="id + '-size'" v-model.number="pageSize" class="bg-card text-card-foreground rounded-md border px-2 py-2 outline-none focus-visible:ring-2 focus-visible:ring-ring">
          <option v-for="size in sizes" :key="size" :value="size">{{ size }}</option>
        </select>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <Button type="button" variant="outline" size="sm" :disabled="page <= 1" aria-label="Previous page" @click="page--">Prev</Button>
        <span aria-live="polite">Page {{ page }} of {{ totalPages }}</span>
        <Button type="button" variant="outline" size="sm" :disabled="page >= totalPages" aria-label="Next page" @click="page++">Next</Button>
      </div>
    </div>
  </div>
</template>
