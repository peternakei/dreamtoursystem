<script setup lang="ts">
import {computed, ref} from 'vue'
import {Car, Eye, ImageOff} from 'lucide-vue-next'
import {Button} from '@/components/ui/button'
import {Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription} from '@/components/ui/dialog'
import {mediaUrl} from './mediaUrl'

const props = defineProps<{offer: any; vehicles: any[]}>()
const preview = ref<any>(null), open = ref(false), failed = ref<Record<string, boolean>>({})
const eligible = computed(() => props.vehicles.filter(v => props.offer.vehicle_uuids?.includes(v.uuid)))
const fields = computed(() => [
  ['Rental purpose', props.offer.purpose], ['Vehicle type', props.offer.vehicle_type],
  ['Quotation unit', props.offer.price_unit], ['Driver', props.offer.driver_policy],
  ['Fuel', props.offer.fuel_policy], ['Duration', props.offer.details?.duration],
  ['Active', props.offer.is_active ? 'Yes' : 'No'], ['Published', props.offer.is_published ? 'Yes' : 'No'],
  ['Featured', props.offer.is_featured ? 'Yes' : 'No'], ['Display order', props.offer.sort_order],
])
const textFields = computed(() => [
  ['Inclusions', props.offer.details?.inclusions], ['Exclusions', props.offer.details?.exclusions],
  ['Mileage and extra-distance terms', props.offer.details?.mileage], ['Terms', props.offer.details?.terms],
])
function show(item: any) { preview.value = item; open.value = true }
</script>

<template>
  <div class="space-y-5">
    <section class="rounded-xl border bg-card p-5 text-card-foreground">
      <div class="flex items-start gap-3"><Car class="mt-1 h-6 w-6 shrink-0 text-primary"/><div class="min-w-0"><h3 class="break-words text-xl font-semibold">{{offer.title}}</h3><p class="mt-1 text-sm text-muted-foreground">Price on request · Staff verify availability before booking</p></div></div>
      <p v-if="offer.description" class="mt-4 whitespace-pre-wrap break-words text-sm">{{offer.description}}</p>
      <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><div v-for="[label, value] in fields" :key="String(label)" class="min-w-0"><dt class="text-xs text-muted-foreground">{{label}}</dt><dd class="mt-1 break-words text-sm font-medium capitalize">{{value === null || value === undefined || value === '' ? 'Not specified' : value}}</dd></div></dl>
    </section>
    <section class="space-y-4 rounded-xl border bg-card p-5 text-card-foreground">
      <h3 class="font-semibold">Photos and media</h3>
      <p v-if="!offer.images?.length" class="text-sm text-muted-foreground">No photos uploaded. Use Edit rental offer to add them.</p>
      <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" aria-label="Rental offer photos">
        <button v-for="m in offer.images" :key="m.uuid || m.id" type="button" class="group min-w-0 overflow-hidden rounded-lg border text-left" :aria-label="'View photo ' + (m.title || m.role || 'image')" @click="show(m)">
          <div class="relative aspect-[4/3] bg-muted/40"><div v-if="failed[m.url]" class="flex h-full items-center justify-center gap-2 text-sm text-muted-foreground"><ImageOff class="h-5 w-5"/>Image unavailable</div><video v-else-if="m.role === 'video'" :src="mediaUrl(m.url)" preload="metadata" class="h-full w-full object-contain"/><img v-else :src="mediaUrl(m.url)" :alt="m.title || offer.title" loading="lazy" class="h-full w-full object-contain p-2" @error="failed[m.url] = true"/><span class="absolute right-2 bottom-2 rounded bg-card/95 p-1.5"><Eye class="h-4 w-4"/></span></div>
          <div class="p-3"><p class="truncate text-sm font-medium">{{m.title || offer.title}}</p><p class="text-xs capitalize text-muted-foreground">{{m.role || 'Gallery'}}</p></div>
        </button>
      </div>
    </section>
    <section class="grid gap-4 sm:grid-cols-2"><article v-for="[label, value] in textFields" :key="label" class="min-w-0 rounded-xl border bg-card p-5 text-card-foreground"><h3 class="font-semibold">{{label}}</h3><p class="mt-2 whitespace-pre-wrap break-words text-sm">{{value || 'Not specified'}}</p></article></section>
    <section class="rounded-xl border bg-card p-5 text-card-foreground"><h3 class="font-semibold">Service locations</h3><ul v-if="offer.details?.service_locations?.length" class="mt-3 flex flex-wrap gap-2"><li v-for="(location, i) in offer.details.service_locations" :key="i" class="rounded-md border px-3 py-1 text-sm">{{location}}</li></ul><p v-else class="mt-2 text-sm text-muted-foreground">Locations are confirmed when preparing the quotation.</p></section>
    <section class="rounded-xl border bg-card p-5 text-card-foreground"><h3 class="font-semibold">Optional extras</h3><div v-if="offer.details?.extras?.length" class="mt-3 grid gap-3 sm:grid-cols-2"><article v-for="extra in offer.details.extras" :key="extra.code" class="rounded-lg border p-3"><h4 class="text-sm font-medium">{{extra.title}}</h4><p class="mt-1 whitespace-pre-wrap break-words text-sm text-muted-foreground">{{extra.description}}</p></article></div><p v-else class="mt-2 text-sm text-muted-foreground">No optional extras configured.</p></section>
    <section class="rounded-xl border bg-card p-5 text-card-foreground"><h3 class="font-semibold">Eligible vehicles</h3><div v-if="eligible.length" class="mt-3 flex flex-wrap gap-2"><RouterLink v-for="v in eligible" :key="v.uuid" :to="'/vehicles/' + v.uuid + '/details'"><Button variant="outline">{{v.name}}</Button></RouterLink></div><p v-else class="mt-2 text-sm text-muted-foreground">Vehicles are selected by type ({{offer.vehicle_type}}), subject to staff checking availability.</p></section>
    <details v-if="Object.keys(offer.translations || {}).length" class="rounded-xl border bg-card p-5 text-card-foreground"><summary class="cursor-pointer font-semibold">Translations</summary><div v-for="(text, locale) in offer.translations" :key="locale" class="mt-4 space-y-1"><h4 class="text-sm font-medium uppercase">{{locale}}</h4><p class="text-sm">{{text.title}}</p><p class="whitespace-pre-wrap text-sm text-muted-foreground">{{text.description}}</p></div></details>
    <Dialog v-model:open="open"><DialogContent class="max-h-[90dvh] w-[calc(100vw_-_2rem)] overflow-y-auto sm:max-w-4xl"><DialogHeader><DialogTitle>{{preview?.title || offer.title}}</DialogTitle><DialogDescription>Rental offer media</DialogDescription></DialogHeader><video v-if="preview?.role === 'video'" :src="mediaUrl(preview.url)" controls class="max-h-[65vh] w-full"/><img v-else-if="preview && !failed[preview.url]" :src="mediaUrl(preview.url)" :alt="preview.title || offer.title" class="max-h-[65vh] w-full object-contain" @error="failed[preview.url] = true"/><p v-else class="text-sm text-muted-foreground">This image could not be loaded.</p></DialogContent></Dialog>
  </div>
</template>
