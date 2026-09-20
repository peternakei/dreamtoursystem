<script setup lang="ts">
import {computed, ref, watch} from 'vue'
import {useRoute,useRouter} from 'vue-router'
import ServiceRequestCreate from '@/components/services/ServiceRequestCreate.vue'
import {Dialog,DialogContent,DialogHeader,DialogTitle,DialogDescription} from '@/components/ui/dialog'
import MainLayout from '@/layouts/MainLayout.vue'
import Field from '@/components/services/ServiceField.vue'
import DetailValues from '@/components/services/DetailValues.vue'
import {dateText, enabled} from '@/components/workspace/listPresentation'
import {formError, options} from '@/components/services/formErrors'
import DataTableReport from '@/components/datatable/DataTableReport.vue'
import {Button} from '@/components/ui/button'
import api from '@/axiosClient'

const router=useRouter(),showCreate=ref(false),creating=ref(false)
async function created(uuid:string){showCreate.value=false;await router.push('/inquiries/'+uuid+'/services')}
function closeCreate(value:boolean){if(!creating.value)showCreate.value=value}
const route = useRoute(), id = computed(() => String(route.params.id ?? '')), rows = ref<any[]>([]),
    record = ref<any>(null), quotes = ref<any[]>([]), currencies = ref<any[]>([]), staff = ref<any[]>([])
const loading = ref(false), busy = ref(false), error = ref(''), notice = ref(''), filter = ref(''), offset = ref(0),
    total = ref(0), showQuote = ref(false)
const form = ref<any>({}), quote = ref<any>({}), quoteId = ref(''), agreed = ref(false)
const columns = [{key: 'inquiry_code', label: 'Reference', sortable: true}, {
  key: 'service_type',
  label: 'Service',
  sortable: true
}, {key: 'customer_name', label: 'Customer', sortable: true}, {
  key: 'customer_email',
  label: 'Email',
  sortable: true
}, {key: 'customer_phone', label: 'Phone'}, {
  key: 'startDate',
  label: 'From',
  sortable: true,
  formatter: dateText
}, {key: 'endDate', label: 'To', sortable: true, formatter: dateText}, {
  key: 'guests',
  label: 'Guests',
  sortable: true
}, {key: 'approval', label: 'Approval', sortable: true}, {
  key: 'status',
  label: 'Status',
  sortable: true
}, {key: 'booking_state', label: 'Booking', sortable: true}, {key: 'actions', label: 'Manage'}]
const tableRows = computed(() => rows.value.map(r => ({
  ...r,
  customer_name: [r.contact?.firstName, r.contact?.lastName].filter(Boolean).join(' '),
  customer_email: r.contact?.email,
  customer_phone: r.contact?.phone,
  approval: enabled(r.is_approved) ? 'Approved' : 'Pending',
  booking_state: r.booking_id ? 'Recorded' : 'Not booked'
})))
const currencyOptions = computed(() => currencies.value.map(c => ({value: c.id, label: c.short_name})))

async function load() {
  loading.value = true;
  error.value = '';
  record.value=null; notice.value=''; showQuote.value=false; quoteId.value=''; agreed.value=false;
  try {
    if (id.value) {
      const {data} = await api.get('/workspace/service-inquiries/' + id.value);
      record.value = data.data.inquiry;
      quotes.value = data.data.quotes;
      currencies.value = data.data.currencies;
      staff.value = data.data.staff;
      form.value = {
        assigned_to: record.value.assigned_to ?? '',
        comments: record.value.comments ?? '',
        is_approved: !!record.value.is_approved,
        operations: {
          internal_notes: '',
          customer_notes: '',
          supply_verified: false,
          issuance_state: 'not_issued',
          booking_reference: '',
          issued_at: '', ...record.value.operations
        }
      }
    } else {
      const {data} = await api.get('/workspace/service-inquiries', {
        params: {
          service_type: filter.value || undefined,
          limit: 25,
          offset: offset.value
        }
      });
      rows.value = data.data.inquiries;
      total.value = data.data.total
    }
  } catch (e) {
    error.value = formError(e)
  } finally {
    loading.value = false
  }
}

async function save() {
  busy.value = true;
  error.value = '';
  try {
    const payload = JSON.parse(JSON.stringify(form.value));
    payload.assigned_to = payload.assigned_to || null;
    const keep = ['internal_notes', 'customer_notes', 'supply_verified', 'issuance_state', 'booking_reference', 'issued_at'];
    payload.operations = Object.fromEntries(Object.entries(payload.operations).filter(([k]) => keep.includes(k)));
    payload.operations.issued_at = payload.operations.issued_at || null;
    await api.put('/workspace/service-inquiries/' + id.value, payload);
    await load();
    notice.value = 'Operations details saved'
  } catch (e) {
    error.value = formError(e)
  } finally {
    busy.value = false
  }
}

function newQuote() {
  quote.value = {
    title: (record.value.offer?.title ?? record.value.service_type) + ' quotation',
    currency_id: record.value.currency_id ?? currencies.value.find(c => c.short_name === 'USD')?.id ?? currencies.value[0]?.id,
    valid_until: '',
    status: 'draft',
    vat_enabled: false,
    terms: '',
    price_lines: [{
      description: '',
      quantity: 1,
      unit_price: null
    }], ...(record.value.service_type === 'flight' ? {
      flight_offer: {
        itinerary: '',
        airline: '',
        cabin: '',
        baggage: '',
        fare_conditions: ''
      }
    } : {})
  };
  showQuote.value = true
}

async function saveQuote() {
  busy.value = true;
  error.value = '';
  try {
    await api.post('/workspace/service-inquiries/' + id.value + '/quotations', quote.value);
    showQuote.value = false;
    await load();
    notice.value = 'Quotation saved'
  } catch (e) {
    error.value = formError(e)
  } finally {
    busy.value = false
  }
}

async function book() {
  busy.value = true;
  error.value = '';
  try {
    await api.post('/workspace/service-inquiries/' + id.value + '/book', {
      quotation_uuid: quoteId.value,
      customer_agreed: agreed.value
    });
    await load();
    notice.value = 'Booking recorded with Reserved status'
  } catch (e) {
    error.value = formError(e)
  } finally {
    busy.value = false
  }
}

watch(() => [id.value, filter.value, offset.value], load, {immediate: true})
</script>
<template>
  <MainLayout :title="record?record.inquiry_code+' · '+record.service_type:'Service requests'">
    <div class="space-y-5">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h2 class="text-xl font-semibold">{{ record ? 'Service request' : 'Service requests' }}</h2>
          <p class="text-sm text-muted-foreground">Existing inquiry workflow · Staff-managed quotations and
            fulfilment</p></div>
        <div class="flex flex-wrap gap-2"><Button v-if="!id" @click="showCreate=true">New service request</Button><RouterLink :to="id?'/service_inquiries/list':'/inquiries/list'">
          <Button variant="outline">{{ id ? 'Service request list' : 'All inquiries' }}</Button>
        </RouterLink></div>
      </div>
      <p v-if="error" role="alert" class="rounded border border-destructive p-3 text-destructive">{{ error }}</p>
      <p v-if="notice" role="status">{{ notice }}</p>
      <template v-if="!id">
        <Field v-model="filter" label="Service" type="select"
               :options="[{value:'',label:'All services'},...options(['rental','business','international','local','group','safari','flight'])]"
               @update:model-value="offset=0"/>
        <DataTableReport :rows="tableRows" :columns="columns" :loading="loading" :searchable="false"
                         :show-date-filter="false" :paginated="false">
          <template #cell-actions="{row}">
            <RouterLink :to="'/inquiries/'+row.uuid+'/services'">
              <Button variant="outline" size="sm">View request</Button>
            </RouterLink>
          </template>
        </DataTableReport>
        <div class="flex items-center gap-3">
          <Button variant="outline" :disabled="loading||offset===0" @click="offset=Math.max(0,offset-25)">Previous
          </Button>
          <span>{{ total }} requests</span>
          <Button variant="outline" :disabled="loading||offset+25>=total" @click="offset+=25">Next</Button>
        </div>
      </template>
      <template v-else-if="record">
        <section class="space-y-4 rounded-xl border bg-card p-5 text-card-foreground"><h3 class="font-semibold">
          {{ record.inquiry_code }} · {{ record.service_type }}</h3>
          <DetailValues
              :value="{contact:record.contact,received_through:record.source?.replaceAll('_',' '),recorded_by:staff.find(s=>s.id===record.created_by)?.username,start_date:record.startDate,end_date:record.endDate,guests:record.guests,language:record.locale,message:record.message,budget:record.budget}"/>
          <h3 class="font-semibold">Requested service</h3>
          <DetailValues :value="record.details"/>
          <details v-if="record.offer">
            <summary class="cursor-pointer font-medium">Offer at the time of request</summary>
            <DetailValues :value="record.offer"/>
          </details>
        </section>
        <form class="space-y-4 rounded-xl border bg-card p-5 text-card-foreground" @submit.prevent="save"><h3
            class="font-semibold">Staff handling</h3>
          <div class="grid gap-4 sm:grid-cols-2">
            <Field v-model="form.assigned_to" label="Assigned staff" type="select"
                   :options="[{value:'',label:'Unassigned'},...staff.map(s=>({value:s.id,label:s.username}))]"/>
            <Field v-model="form.is_approved" label="Inquiry approved" type="checkbox"/>
            <Field v-model="form.comments" label="Inquiry comments" type="textarea"/>
            <Field v-model="form.operations.internal_notes" label="Private operational notes" type="textarea"/>
            <Field v-model="form.operations.customer_notes" label="Update visible to the customer" type="textarea"/>
          </div>
          <Field v-if="record.service_type==='rental'" v-model="form.operations.supply_verified"
                 label="I have checked supplier availability for the requested vehicles and dates" type="checkbox"/>
          <div v-if="record.service_type==='flight'" class="grid gap-4 sm:grid-cols-2">
            <Field v-model="form.operations.issuance_state" label="Ticket handling state" type="select"
                   :options="options(['not_issued','confirmed','issued','cancelled'])"/>
            <Field v-model="form.operations.booking_reference" label="Actual airline booking reference"/>
            <Field v-model="form.operations.issued_at" label="Actual issue time" type="datetime-local"/>
          </div>
          <DetailValues v-if="record.operations?.cancellation_requested"
                        :value="{cancellation_requested:true,reason:record.operations.cancellation_reason}"/>
          <Button :disabled="busy" type="submit">Save handling details</Button>
        </form>
        <section class="space-y-4 rounded-xl border bg-card p-5 text-card-foreground">
          <div class="flex justify-between gap-3"><h3 class="font-semibold">Quotations</h3>
            <Button @click="newQuote">Create quotation</Button>
          </div>
          <div v-for="q in quotes" :key="q.uuid" class="rounded border p-3"><p class="font-medium">
            {{ q.reference_number }} · {{ q.title }}</p>
            <p class="text-sm">{{ q.currency?.short_name }} {{ q.total_amount }} · {{ q.status }} · Valid until
              {{ q.service_context?.valid_until }}</p>
            <p class="mt-2 whitespace-pre-wrap text-sm">{{ q.service_context?.terms }}</p>
            <DetailValues v-if="q.service_context?.flight_offer" :value="q.service_context.flight_offer"/>
          </div>
          <p v-if="!quotes.length" class="text-sm text-muted-foreground">No quotation recorded.</p></section>
        <form v-if="showQuote" class="space-y-4 rounded-xl border bg-card p-5 text-card-foreground"
              @submit.prevent="saveQuote"><h3 class="font-semibold">New quotation</h3>
          <div class="grid gap-4 sm:grid-cols-2">
            <Field v-model="quote.title" label="Title" required/>
            <Field v-model="quote.currency_id" label="Currency" type="select" :options="currencyOptions" required/>
            <Field v-model="quote.valid_until" label="Valid until" type="datetime-local" required/>
            <Field v-model="quote.status" label="Status" type="select" :options="options(['draft','sent'])"/>
            <Field v-model="quote.vat_enabled" label="Apply existing VAT rule" type="checkbox"/>
          </div>
          <div v-for="(line,i) in quote.price_lines" :key="i"
               class="grid items-end gap-3 sm:grid-cols-[2fr_1fr_1fr_auto]">
            <Field v-model="line.description" label="Line description" required/>
            <Field v-model="line.quantity" label="Quantity" type="number" :min="1" required/>
            <Field v-model="line.unit_price" label="Unit price" type="number" :min="0.01" step="0.01" required/>
            <Button type="button" variant="outline" :disabled="quote.price_lines.length===1"
                    @click="quote.price_lines.splice(i,1)">Remove
            </Button>
          </div>
          <Button type="button" variant="outline"
                  @click="quote.price_lines.push({description:'',quantity:1,unit_price:null})">Add price line
          </Button>
          <Field v-model="quote.terms" label="Agreed scope, exclusions and cancellation terms" type="textarea"
                 required/>
          <div v-if="quote.flight_offer" class="grid gap-4 sm:grid-cols-2">
            <Field v-for="key in ['itinerary','airline','cabin','baggage','fare_conditions']" :key="key"
                   v-model="quote.flight_offer[key]" :label="key.replaceAll('_',' ')" type="textarea" required/>
          </div>
          <div class="flex gap-2">
            <Button :disabled="busy" type="submit">Save quotation</Button>
            <Button type="button" variant="outline" @click="showQuote=false">Close</Button>
          </div>
        </form>
        <section v-if="record.booking_id" class="space-y-3 rounded-xl border bg-card p-5 text-card-foreground"><h3
            class="font-semibold">Agreed booking</h3>
          <DetailValues :value="record.agreed_price"/>
          <RouterLink to="/bookings/list" class="underline">Manage booking confirmation, payment and cancellation
          </RouterLink>
        </section>
        <form v-else-if="quotes.some(q=>q.status==='sent')"
              class="space-y-4 rounded-xl border bg-card p-5 text-card-foreground" @submit.prevent="book"><h3
            class="font-semibold">Record an agreed booking</h3>
          <Field v-model="quoteId" label="Sent quotation" type="select"
                 :options="[{value:'',label:'Select quotation'},...quotes.filter(q=>q.status==='sent').map(q=>({value:q.uuid,label:q.reference_number+' · '+q.currency?.short_name+' '+q.total_amount}))]"
                 required/>
          <Field v-model="agreed" label="The customer has explicitly agreed to this quotation and its terms"
                 type="checkbox"/>
          <p class="text-sm text-muted-foreground">Rental supply must be checked first. This creates a Reserved booking;
            it does not record payment or issue a flight ticket.</p>
          <Button type="submit" :disabled="busy||!agreed||!quoteId">Record agreed booking</Button>
        </form>
      </template>
      <p v-else-if="loading" role="status">Loading request…</p><Dialog :open="showCreate" @update:open="closeCreate"><DialogContent class="max-h-[90dvh] w-[calc(100vw_-_2rem)] overflow-y-auto sm:max-w-4xl" @escape-key-down="creating && $event.preventDefault()" @interact-outside="creating && $event.preventDefault()"><DialogHeader><DialogTitle>New service request</DialogTitle><DialogDescription>Record a customer request received by phone, WhatsApp, email or in person.</DialogDescription></DialogHeader><ServiceRequestCreate v-if="showCreate" :initial-service="filter || 'business'" @busy="creating=$event" @created="created"/></DialogContent></Dialog></div>
  </MainLayout>
</template>
