<script setup lang="ts">
import {computed} from 'vue'
import {dateText,enabled,text} from './listPresentation'
const props=defineProps<{module:string;record:Record<string,any>}>()
type Entry={label:string;value:string}
const at=(path:string)=>path.split('.').reduce<any>((value,key)=>value?.[key],props.record)
const money=(value:any)=>value==null?'—':`${props.record.currency?.short_name||''} ${Number(value).toLocaleString('en-GB',{minimumFractionDigits:2,maximumFractionDigits:2})}`.trim()
const definitions:Record<string,[string,string][]>={
 bookings:[['booking_number','Reference'],['tourist.name','Customer'],['tourist.email','Email'],['tourist.phone','Phone'],['trip.name','Trip'],['booking_type.name','Booking type'],['booking_date','Travel date'],['guest_count','Guests'],['status.name','Status'],['amount','Amount'],['vat_amount','VAT'],['total_amount','Total'],['remarks','Remarks'],['comments','Comments'],['created_at','Created']],
 tourists:[['tourist_number','Reference'],['name','Name'],['email','Email'],['phone','Phone'],['gender.name','Gender'],['country.name','Country'],['address','Address'],['is_active','State'],['created_at','Created'],['updated_at','Updated']],
 invoices:[['invoice_number','Invoice'],['tourist.name','Customer'],['booking.booking_number','Booking'],['invoice_date','Invoice date'],['status.name','Status'],['amount','Amount'],['vat_amount','VAT'],['total_amount','Total'],['exchange_rate','Exchange rate'],['remarks','Remarks'],['created_at','Created']],
 receipts:[['reference_number','Reference'],['invoice.invoice_number','Invoice'],['invoice.tourist.name','Customer'],['receipt_date','Receipt date'],['amount','Amount'],['payment_mode.name','Payment method'],['cheque_number','Cheque number'],['cheque_date','Cheque date'],['remarks','Remarks'],['created_at','Created']],
}
const entries=computed<Entry[]>(()=>{
 const fields=definitions[props.module]||Object.keys(props.record).filter(key=>!/(^id$|_id$|^uuid$|password|token|secret|^deleted_at$|^created_by$|^updated_by$)/i.test(key)&&!Array.isArray(props.record[key])&&(!props.record[key]||typeof props.record[key]!=='object'||props.record[key].name||props.record[key].title||props.record[key].username)).map(key=>[key,key.replaceAll('_',' ').replace(/\b\w/g,c=>c.toUpperCase())]);
 return fields.map(([path,label])=>{const value=at(path);return {label,value: ['amount','vat_amount','total_amount'].includes(path)?money(value):path==='is_active'?(enabled(value)?'Active':'Inactive'):path.endsWith('_date')||path.endsWith('_at')?dateText(value):text(value)}})
})
</script>
<template><section class="rounded-xl border bg-card p-5 text-card-foreground"><h3 class="mb-5 font-semibold">Record details</h3><dl class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3"><div v-for="entry in entries" :key="entry.label" class="min-w-0"><dt class="text-xs font-medium uppercase tracking-wide text-muted-foreground">{{entry.label}}</dt><dd class="mt-1 whitespace-pre-wrap break-words text-sm">{{entry.value}}</dd></div></dl></section></template>
