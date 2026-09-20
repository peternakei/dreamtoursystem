<script setup lang="ts">
import {computed, ref, watch} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {ArrowLeft, RefreshCw} from 'lucide-vue-next'
import MainLayout from '@/layouts/MainLayout.vue'
import {Button} from '@/components/ui/button'
import {Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription} from '@/components/ui/dialog'
import DataTableReport from '@/components/datatable/DataTableReport.vue'
import EntityForm from '@/components/workspace/EntityForm.vue'
import {text, dateText, enabled} from '@/components/workspace/listPresentation'
import type {Page, Form} from '@/components/workspace/types'
import api, {errorMessage} from '@/axiosClient'
import {toast} from 'vue-sonner'

const route = useRoute(), router = useRouter()
const page = ref<Page | null>(null), loading = ref(false), error = ref('')
const selected = ref<Form | null>(null), open = ref(false)
const inquiry = computed(() => page.value?.details.inquiry)
const quotes = computed(() => (inquiry.value?.quotations || []).map((q: any) => ({
  ...q, quote_title: q.current_version?.title, currency_code: q.currency?.short_name ?? q.current_version?.currency?.short_name,
  status_name: q.status?.name,
})))
const quoteColumns = [
  {key: 'quotation_number', label: 'Reference', sortable: true},
  {key: 'quote_title', label: 'Title', sortable: true},
  {key: 'quotation_date', label: 'Date', sortable: true, formatter: dateText},
  {key: 'currency_code', label: 'Currency'},
  {key: 'total_amount', label: 'Total', sortable: true, formatter: (v: any) => v == null ? '—' : Number(v).toLocaleString('en-GB', {minimumFractionDigits: 2, maximumFractionDigits: 2})},
  {key: 'status_name', label: 'Status', sortable: true},
  {key: 'actions', label: 'Actions'},
]
const sections = computed(() => {
  const i = inquiry.value || {}, t = i.tourist || {}
  return [
    {title: 'Customer', fields: {'Name': t.name, 'Email': t.email, 'Phone': t.phone, 'Country': t.country?.name}},
    {title: 'Travel requirements', fields: {'Requested trip': i.tour_title, 'Trip type': i.trip_type?.name, 'Service class': i.service_class?.name, 'From': dateText(i.from_date), 'To': dateText(i.to_date), 'Guests': i.guests, 'Budget preference': i.budget, 'Language': i.communication_language}},
    {title: 'Staff handling', fields: {'Approval': enabled(i.is_approved) ? 'Approved' : 'Pending', 'Assigned to': i.assigned_to?.username, 'Source': i.source, 'Received': dateText(i.received_at || i.created_at), 'Comments': i.comments}},
  ]
})
async function load() {
  loading.value = true; error.value = ''; page.value = null
  const id = String(route.params.id)
  try {
    const result = (await api.get('/workspace/modules/inquiries/' + id)).data as Page
    if (String(route.params.id) !== id) return
    if (result.details.inquiry?.service_details_exists) {
      await router.replace('/inquiries/' + id + '/services')
      return
    }
    page.value = result
  } catch (e) { error.value = errorMessage(e) }
  finally { loading.value = false }
}
function choose(form: Form) { selected.value = form; open.value = true }
async function saved() { open.value = false; toast.success('Inquiry updated'); await load() }
watch(() => route.params.id, load, {immediate: true})
</script>

<template>
  <MainLayout :title="inquiry?.inquiry_code || 'Inquiry details'">
    <div class="space-y-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <RouterLink to="/inquiries/list"><Button variant="outline" size="icon" aria-label="Back to inquiries"><ArrowLeft class="h-4 w-4" /></Button></RouterLink>
          <div><h2 class="text-xl font-semibold">{{ inquiry?.inquiry_code || 'Inquiry details' }}</h2><p class="text-sm text-muted-foreground">Customer requirements, approval and quotations</p></div>
        </div>
        <div class="flex flex-wrap gap-2">
          <Button variant="outline" :disabled="loading" @click="load"><RefreshCw class="h-4 w-4" /> Refresh</Button>
          <Button v-for="(form, index) in page?.forms || []" :key="index" @click="choose(form)">{{ form.action.includes('change_status') ? 'Change approval' : form.title }}</Button>
        </div>
      </div>
      <p v-if="error" role="alert" class="rounded border border-destructive p-4 text-destructive">{{ error }}</p>
      <p v-if="loading" role="status">Loading inquiry…</p>
      <template v-else-if="inquiry">
        <div class="grid gap-5 xl:grid-cols-3">
          <section v-for="section in sections" :key="section.title" class="min-w-0 rounded-xl border bg-card p-5 text-card-foreground">
            <h3 class="mb-4 font-semibold">{{ section.title }}</h3>
            <dl class="space-y-4"><div v-for="(value, key) in section.fields" :key="key"><dt class="text-xs text-muted-foreground">{{ key }}</dt><dd class="mt-1 whitespace-pre-wrap break-words text-sm">{{ text(value) }}</dd></div></dl>
          </section>
        </div>
        <section class="rounded-xl border bg-card p-5 text-card-foreground"><h3 class="mb-3 font-semibold">Customer message</h3><p class="whitespace-pre-wrap break-words text-sm">{{ inquiry.client_message || inquiry.description || 'No message recorded.' }}</p></section>
        <section class="space-y-4">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold">Quotations <span class="text-muted-foreground">({{ quotes.length }})</span></h3>
            <RouterLink v-if="page?.actions?.can_create_quotation" :to="'/inquiries/'+route.params.id+'/quotations/new'"><Button variant="outline">Create trip quotation</Button></RouterLink>
          </div>
          <p v-if="!enabled(inquiry.is_approved)" class="text-sm text-muted-foreground">Approve this inquiry before preparing a trip quotation.</p>
          <DataTableReport :rows="quotes" :columns="quoteColumns" :show-date-filter="false" :searchable="false">
            <template #cell-actions="{row}"><RouterLink :to="'/quotations/'+row.uuid+'/details'"><Button variant="outline" size="sm">View quotation</Button></RouterLink></template>
          </DataTableReport>
        </section>
      </template>
    </div>
    <Dialog v-model:open="open"><DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-2xl"><DialogHeader><DialogTitle>{{ selected?.title }}</DialogTitle><DialogDescription>Update the inquiry using the existing approval workflow.</DialogDescription></DialogHeader><EntityForm v-if="selected" :form="selected" @saved="saved" /></DialogContent></Dialog>
  </MainLayout>
</template>
