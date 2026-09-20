<script setup lang="ts">
import {computed, onMounted, ref, watch} from 'vue'
import Field from './ServiceField.vue'
import {Button} from '@/components/ui/button'
import {formError, options} from './formErrors'
import api from '@/axiosClient'

const props = defineProps<{initialService?: string}>()
const emit = defineEmits<{created: [uuid: string]; busy: [value: boolean]}>()
const serviceTypes = [{value:'business',label:'Business travel'},{value:'flight',label:'Air ticketing'},{value:'rental',label:'Car rental'},{value:'international',label:'International travel'},{value:'local',label:'Local travel'},{value:'group',label:'Group travel'},{value:'safari',label:'Safari'}]
const catalog = ref<any>({countries:[],currencies:[],staff:[],offers:[],vehicles:[],vehicle_types:[],trips:[],destinations:[],classes:[]})
const loading = ref(true), busy = ref(false), error = ref(''), errors = ref<string[]>([])
const form = ref<any>({service_type:props.initialService || 'business',source:'phone',assigned_to:'',staff_notes:'',firstName:'',lastName:'',email:'',phone:'',country:'',locale:'en',currency_id:'',message:'',budget:'',startDate:'',endDate:'',guests:1,trip_uuid:'',destinations:[],serviceClass:'',details:{}})
const detail = computed(() => form.value.details)
const offer = computed(() => catalog.value.offers.find((o:any) => o.uuid === detail.value.offer_uuid))
const travel = computed(() => !['rental','flight'].includes(form.value.service_type))
const choices = (items:any[], key='id', label='name') => [{value:'',label:'Not specified'}, ...items.map(i => ({value:i[key],label:i[label]}))]
const vehicles = computed(() => catalog.value.vehicles.filter((v:any) => v.rental_specs?.vehicle_type === detail.value.vehicle_type && (!offer.value?.vehicle_uuids?.length || offer.value.vehicle_uuids.includes(v.uuid))))
const driverChoices = computed(() => offer.value?.driver_policy === 'included' ? [{value:'with_driver',label:'With driver'}] : offer.value?.driver_policy === 'excluded' ? [{value:'self_drive',label:'Self drive'}] : [{value:'with_driver',label:'With driver'},{value:'self_drive',label:'Self drive'}])
const leg = () => ({origin:'',destination:'',departure_date:''})
watch(() => form.value.service_type, type => {
  errors.value = []; error.value = ''
  form.value.details = type === 'rental' ? {offer_uuid:'',vehicle_uuid:'',purpose:'private',vehicle_type:'sedan',start_at:'',end_at:'',quantity:1,passengers:1,driver:'with_driver',fuel:'',pickup:'',dropoff:'',extras:[]} : type === 'flight' ? {trip_type:'one_way',geography:'domestic',adults:1,children:0,infants:0,cabin:'economy',flexibility:'',airline_preference:'',group_travel:false,legs:[leg()]} : {company:'',contact_person:'',industry:'',purpose:'',geography:'',country_city:'',sector:'',destination_region:'',meeting_details:'',transport:'',accommodation:'',places_of_interest:'',selected_services:[],customized:false,route:''}
}, {immediate:true})
watch(() => detail.value.trip_type, type => {
  if (!type) return
  const old = detail.value.legs || []
  detail.value.legs = type === 'one_way' ? [old[0] || leg()] : [old[0] || leg(), old[1] || leg(), ...(type === 'multi_city' ? old.slice(2) : [])]
})
watch(() => [detail.value.trip_type, detail.value.legs?.[0]?.origin, detail.value.legs?.[0]?.destination], () => {
  if (detail.value.trip_type === 'round_trip' && detail.value.legs?.[1]) {
    detail.value.legs[1].origin = detail.value.legs[0].destination
    detail.value.legs[1].destination = detail.value.legs[0].origin
  }
})
function selectOffer(uuid:string) {
  const selected = catalog.value.offers.find((o:any) => o.uuid === uuid)
  detail.value.vehicle_uuid = ''; detail.value.extras = []
  if (selected) Object.assign(detail.value, {purpose:selected.purpose,vehicle_type:selected.vehicle_type,driver:selected.driver_policy === 'excluded' ? 'self_drive' : 'with_driver',fuel:selected.fuel_policy === 'selectable' ? '' : selected.fuel_policy})
}
async function load() {
  loading.value = true; error.value = ''
  try { catalog.value = (await api.get('/workspace/service-inquiries/options')).data.data }
  catch (e) { error.value = formError(e) }
  finally { loading.value = false }
}
async function submit() {
  busy.value = true; emit('busy', true); error.value = ''; errors.value = []
  try {
    const f = form.value
    const payload:any = Object.fromEntries(['service_type','source','firstName','lastName','email','phone','country','locale','message','budget','staff_notes','details'].map(k => [k,f[k]]))
    payload.assigned_to = f.assigned_to || null; payload.currency_id = f.currency_id || null
    if (travel.value) Object.assign(payload, {startDate:f.startDate,endDate:f.endDate,guests:f.guests,trip_uuid:f.trip_uuid || null,destinations:f.destinations,serviceClass:f.serviceClass || null})
    const {data} = await api.post('/workspace/service-inquiries', payload)
    emit('created', data.data.inquiry.uuid)
  } catch (e:any) { error.value = formError(e); errors.value = Object.values(e.response?.data?.errors || {}).flat() as string[] }
  finally { busy.value = false; emit('busy', false) }
}
onMounted(load)
</script>

<template>
  <p v-if="loading" role="status" class="text-sm">Loading request options…</p>
  <div v-else-if="!catalog.countries.length && error" class="space-y-3"><p role="alert" class="text-destructive">{{error}}</p><Button variant="outline" @click="load">Retry</Button></div>
  <form v-else class="space-y-5" @submit.prevent="submit">
    <div v-if="error" role="alert" class="rounded-md bg-destructive/10 p-3 text-sm text-destructive"><p>{{error}}</p><ul v-if="errors.length" class="mt-2 list-disc space-y-1 pl-5"><li v-for="message in errors" :key="message">{{message}}</li></ul></div>
    <fieldset :disabled="busy" class="min-w-0 space-y-5">
      <div class="grid gap-4 sm:grid-cols-2"><Field v-model="form.service_type" label="Service" type="select" :options="serviceTypes" required/><Field v-model="form.source" label="Received through" type="select" :options="[{value:'phone',label:'Phone'},{value:'whatsapp',label:'WhatsApp'},{value:'walk_in',label:'Walk-in'},{value:'email',label:'Email'},{value:'office',label:'Office / other'}]" required/></div>
      <section class="space-y-3 rounded-lg border p-4"><h3 class="font-semibold">Customer details</h3><div class="grid gap-3 sm:grid-cols-2"><Field v-model="form.firstName" label="First name" required/><Field v-model="form.lastName" label="Last name" required/><Field v-model="form.email" label="Email" type="email" required/><Field v-model="form.phone" label="Phone" type="tel" required/><Field v-model="form.country" label="Country" type="select" :options="[{value:'',label:'Select country'},...catalog.countries.map((c:any)=>({value:c.id,label:c.name}))]" required/><Field v-model="form.locale" label="Communication language" type="select" :options="[{value:'en',label:'English'},{value:'fr',label:'French'},{value:'sw',label:'Swahili'}]"/></div><p class="text-xs text-muted-foreground">For an existing customer, use their recorded email and phone number.</p></section>
      <section v-if="travel" class="space-y-4 rounded-lg border p-4"><h3 class="font-semibold">Travel requirements</h3><div class="grid gap-3 sm:grid-cols-2"><Field v-model="form.startDate" label="Start date" type="date" required/><Field v-model="form.endDate" label="End date" type="date" required/><Field v-model="form.guests" label="Travellers" type="number" :min="1" required/><Field v-model="detail.purpose" label="Purpose of travel" required/>
        <template v-if="form.service_type==='business'"><Field v-model="detail.company" label="Company" required/><Field v-model="detail.contact_person" label="Contact person" required/><Field v-model="detail.industry" label="Industry"/><Field v-model="detail.geography" label="Travel area" type="select" :options="[{value:'',label:'Not specified'},...options(['local','international'])]"/></template>
        <template v-if="form.service_type==='international'"><Field v-model="detail.country_city" label="Destination country / city" required/><Field v-model="detail.sector" label="Sector" type="select" :options="[{value:'',label:'Not specified'},...options(['agriculture','mining','investment','trade','tourism'])]"/></template>
        <Field v-if="form.service_type==='local'" v-model="detail.destination_region" label="Destination / region" required/>
        <template v-if="['group','safari'].includes(form.service_type)"><Field v-model="detail.route" label="Preferred route"/><Field v-model="detail.customized" label="Customized itinerary" type="checkbox"/></template>
        <Field v-model="detail.transport" label="Transport requirements"/><Field v-model="detail.accommodation" label="Accommodation requirements"/>
      </div><Field v-if="form.service_type==='business'" v-model="detail.meeting_details" label="Meetings / business requirements" type="textarea"/><Field v-model="detail.places_of_interest" label="Places of interest" type="textarea"/><Field :model-value="detail.selected_services.join('\n')" label="Requested services (one per line)" type="textarea" @update:model-value="detail.selected_services=$event.split('\n').map((s:string)=>s.trim()).filter(Boolean)"/>
      <details class="rounded border p-3"><summary class="cursor-pointer text-sm font-medium">Optional trip and destination preferences</summary><div class="mt-3 grid gap-3 sm:grid-cols-2"><Field v-model="form.trip_uuid" label="Published trip" type="select" :options="choices(catalog.trips,'uuid')"/><Field v-model="form.serviceClass" label="Service class" type="select" :options="choices(catalog.classes)"/></div><div class="mt-3 grid max-h-48 gap-2 overflow-y-auto sm:grid-cols-2"><label v-for="d in catalog.destinations" :key="d.id" class="flex items-center gap-2 text-sm"><input v-model="form.destinations" type="checkbox" :value="d.id"/>{{d.name}}</label></div></details></section>
      <section v-else-if="form.service_type==='rental'" class="space-y-4 rounded-lg border p-4"><h3 class="font-semibold">Car rental requirements</h3><Field v-model="detail.offer_uuid" label="Published rental offer (optional)" type="select" :options="choices(catalog.offers,'uuid','title')" @update:model-value="selectOffer"/><div class="grid gap-3 sm:grid-cols-2"><Field v-model="detail.purpose" label="Rental purpose" type="select" :options="options(['private','corporate','wedding','event'])" :disabled="!!offer"/><Field v-model="detail.vehicle_type" label="Vehicle type" type="select" :options="options(catalog.vehicle_types)" :disabled="!!offer" @update:model-value="detail.vehicle_uuid=''"/><Field v-model="detail.vehicle_uuid" label="Preferred vehicle (optional)" type="select" :options="choices(vehicles,'uuid')"/><Field v-model="detail.quantity" label="Number of vehicles" type="number" :min="1" required/><Field v-model="detail.start_at" label="Pickup date and time" type="datetime-local" required/><Field v-model="detail.end_at" label="Return date and time" type="datetime-local" required/><Field v-model="detail.pickup" label="Pickup location" required/><Field v-model="detail.dropoff" label="Drop-off location" required/><Field v-model="detail.passengers" label="Passengers" type="number" :min="1"/><Field v-model="detail.driver" label="Driver preference" type="select" :options="driverChoices"/><Field v-model="detail.fuel" label="Fuel" type="select" :options="[{value:'',label:'Confirm when quoting'},...options(['included','excluded'])]" :required="offer?.fuel_policy==='selectable'" :disabled="!!offer && offer.fuel_policy!=='selectable'"/></div><p class="text-xs text-muted-foreground">Times use {{catalog.timezone}}. Pricing and vehicle availability require staff confirmation.</p><fieldset v-if="offer?.details?.extras?.length" class="space-y-2"><legend class="mb-2 text-sm font-medium">Requested extras</legend><label v-for="extra in offer.details.extras" :key="extra.code" class="flex items-center gap-2 text-sm"><input v-model="detail.extras" type="checkbox" :value="extra.code"/>{{extra.title}}</label></fieldset></section>
      <section v-else class="space-y-4 rounded-lg border p-4"><h3 class="font-semibold">Flight requirements</h3><div class="grid gap-3 sm:grid-cols-2"><Field v-model="detail.trip_type" label="Journey type" type="select" :options="options(['one_way','round_trip','multi_city'])"/><Field v-model="detail.geography" label="Flight area" type="select" :options="options(['domestic','international'])"/><Field v-model="detail.adults" label="Adults" type="number" :min="1" required/><Field v-model="detail.children" label="Children" type="number" :min="0"/><Field v-model="detail.infants" label="Infants" type="number" :min="0"/><Field v-model="detail.cabin" label="Cabin" type="select" :options="options(['economy','premium_economy','business','first'])"/><Field v-model="detail.airline_preference" label="Preferred airline"/><Field v-model="detail.flexibility" label="Date flexibility"/><Field v-model="detail.group_travel" label="Group travel" type="checkbox"/></div><article v-for="(item,i) in detail.legs" :key="i" class="space-y-3 rounded border p-3"><h4 class="text-sm font-medium">Flight leg {{i+1}}</h4><div class="grid gap-3 sm:grid-cols-3"><Field v-model="item.origin" label="Origin" :disabled="detail.trip_type==='round_trip' && i===1" required/><Field v-model="item.destination" label="Destination" :disabled="detail.trip_type==='round_trip' && i===1" required/><Field v-model="item.departure_date" label="Departure date" type="date" required/></div><Button v-if="detail.trip_type==='multi_city'" type="button" variant="outline" size="sm" :disabled="detail.legs.length<=2" @click="detail.legs.splice(i,1)">Remove leg</Button></article><Button v-if="detail.trip_type==='multi_city'" type="button" variant="outline" @click="detail.legs.push(leg())">Add flight leg</Button></section>
      <section class="space-y-3 rounded-lg border p-4"><h3 class="font-semibold">Request and staff handling</h3><Field v-model="form.message" label="Customer request / message" type="textarea" required/><div class="grid gap-3 sm:grid-cols-2"><Field v-model="form.budget" label="Customer budget (optional)"/><Field v-model="form.currency_id" label="Preferred currency" type="select" :options="choices(catalog.currencies,'id','short_name')"/><Field v-model="form.assigned_to" label="Assign to staff" type="select" :options="[{value:'',label:'Unassigned'},...catalog.staff.map((s:any)=>({value:s.id,label:s.username}))]"/></div><Field v-model="form.staff_notes" label="Private staff notes" type="textarea"/></section>
      <p class="text-sm text-muted-foreground">This saves a pending request. Prepare a quotation and record the customer's agreement before making a booking.</p><Button type="submit" :disabled="busy">{{busy?'Creating…':'Create service request'}}</Button>
    </fieldset>
  </form>
</template>
