<script setup lang="ts">
import {computed, ref} from 'vue'
import {Images, Eye, ImageOff, ArrowUp, ArrowDown, Trash2} from 'lucide-vue-next'
import {mediaUrl} from './mediaUrl'
import FilePicker from './FilePicker.vue'
import api, {errorMessage} from '@/axiosClient'
import {Button} from '@/components/ui/button'
import {Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription} from '@/components/ui/dialog'

const props = defineProps<{type: string; uuid: string; images: any[]; disabled?: boolean}>()
const emit = defineEmits(['saved'])
const role = ref('gallery'), title = ref(''), files = ref<File[]>([])
const busy = ref(false), error = ref(''), progress = ref<number | null>(null)
const preview = ref<any>(null), open = ref(false), titles = ref<Record<number, string>>({})
const failed = ref<Record<string, boolean>>({})
const locked = computed(() => busy.value || props.disabled)
const headers = {'X-Safari-Workspace': '1'}
const isPdf = (m: any) => /\.pdf(?:\?|$)/i.test(m.name || m.url || '')
const accept = computed(() => role.value === 'video' ? 'video/mp4,video/webm,video/quicktime,video/x-m4v' : 'image/jpeg,image/png,image/webp,image/gif,image/bmp')

async function run(action: () => Promise<unknown>) {
  if (locked.value) return false
  busy.value = true
  error.value = ''
  try { await action(); emit('saved'); return true }
  catch (e: any) {
    const messages = Object.values(e.response?.data?.errors || {}).flat()
    error.value = messages.length ? messages.join(' ') : errorMessage(e)
    return false
  } finally { busy.value = false }
}
async function upload() {
  if (!files.value.length) return
  await run(async () => {
    const data = new FormData()
    data.append('role', role.value)
    data.append('title', title.value)
    files.value.forEach(file => data.append('files[]', file))
    progress.value = 0
    try {
      await api.post('/library/' + props.type + '/' + props.uuid + '/media', data, {
        headers,
        onUploadProgress: event => { if (event.total) progress.value = Math.round(event.loaded * 100 / event.total) },
      })
      files.value = []
      title.value = ''
    } finally { progress.value = null }
  })
}
async function remove(id: number) {
  if (!window.confirm('Remove this media item?')) return
  await run(() => api.delete('/library/media/' + id, {headers}))
}
async function rename(m: any) {
  if (await run(() => api.patch('/library/media/' + m.id, {title: titles.value[m.id] ?? m.title ?? ''}, {headers}))) delete titles.value[m.id]
}
function siblings(item: any) { return props.images.filter(m => (m.role || 'gallery') === (item.role || 'gallery')) }
function canMove(item: any, direction: number) {
  const items = siblings(item), at = items.findIndex(m => m.id === item.id)
  return at + direction >= 0 && at + direction < items.length
}
async function move(item: any, direction: number) {
  if (!canMove(item, direction)) return
  const items = siblings(item), at = items.findIndex(m => m.id === item.id)
  ;[items[at], items[at + direction]] = [items[at + direction], items[at]]
  await run(() => api.post('/library/' + props.type + '/' + props.uuid + '/media/reorder', {role: item.role || 'gallery', order: items.map(m => m.id)}, {headers}))
}
function show(item: any) { preview.value = item; open.value = true }
</script>

<template>
  <section class="min-w-0 space-y-5 rounded-xl border bg-card p-4 text-card-foreground sm:p-5" aria-label="Media library">
    <div class="flex items-center justify-between gap-3"><div><h3 class="font-semibold">Photos and media</h3><p class="mt-1 text-sm text-muted-foreground">{{images.length}} uploaded · Click a photo to view it in full.</p></div><Images class="h-6 w-6 shrink-0 text-muted-foreground"/></div>
    <p v-if="error" role="alert" class="rounded-md bg-destructive/10 p-3 text-sm text-destructive">{{error}}</p>
    <div v-if="!images.length" class="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground">No photos yet. Choose files below to add your first image.</div>
    <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="Uploaded media">
      <article v-for="m in images" :key="m.uuid || m.id" class="min-w-0 overflow-hidden rounded-lg border">
        <button type="button" class="group relative block aspect-[4/3] w-full bg-muted/40" :aria-label="'Preview ' + (m.title || m.role || 'image')" @click="show(m)">
          <div v-if="failed[m.url]" class="flex h-full flex-col items-center justify-center gap-2 text-sm text-muted-foreground"><ImageOff class="h-8 w-8"/>Image unavailable</div>
          <video v-else-if="m.role === 'video'" :src="mediaUrl(m.url)" preload="metadata" class="h-full w-full object-contain"/>
          <p v-else-if="isPdf(m)" class="p-8">PDF document</p>
          <img v-else :src="mediaUrl(m.url)" :alt="m.title || 'Uploaded image'" loading="lazy" class="h-full w-full object-contain p-2" @error="failed[m.url] = true"/>
          <span class="absolute left-2 top-2 rounded bg-card/95 px-2 py-1 text-xs font-medium capitalize">{{m.role || 'Gallery'}}</span>
          <span class="absolute bottom-2 right-2 flex items-center gap-1 rounded bg-card/95 px-2 py-1 text-xs"><Eye class="h-3.5 w-3.5"/>View</span>
        </button>
        <div class="space-y-3 p-3">
          <label class="block space-y-1 text-xs text-muted-foreground"><span>Image title</span><input :value="titles[m.id] ?? m.title ?? ''" :aria-label="'Title for media ' + m.id" :disabled="locked" maxlength="150" placeholder="Add a descriptive title" class="w-full min-w-0 rounded border bg-card p-2 text-sm text-card-foreground" @input="titles[m.id] = ($event.target as HTMLInputElement).value"/></label>
          <div class="flex flex-wrap items-center gap-1"><Button type="button" size="sm" variant="outline" :disabled="locked" @click="rename(m)">Save title</Button><Button type="button" size="icon" variant="ghost" :disabled="locked || !canMove(m, -1)" aria-label="Move media earlier" @click="move(m, -1)"><ArrowUp class="h-4 w-4"/></Button><Button type="button" size="icon" variant="ghost" :disabled="locked || !canMove(m, 1)" aria-label="Move media later" @click="move(m, 1)"><ArrowDown class="h-4 w-4"/></Button><Button type="button" size="icon" variant="ghost" :disabled="locked" :aria-label="'Remove media ' + (m.title || m.id)" @click="remove(m.id)"><Trash2 class="h-4 w-4 text-destructive"/></Button></div>
        </div>
      </article>
    </div>
    <!-- This component also lives inside content editors; never nest another form. -->
    <div class="space-y-4 border-t pt-4" role="group" aria-label="Upload media">
      <h4 class="text-sm font-medium">Add photos or video</h4>
      <div class="grid gap-3 sm:grid-cols-2"><label class="text-sm">Media role<select v-model="role" :disabled="locked" class="mt-1 block w-full rounded border bg-card p-2"><option value="gallery">Gallery</option><option value="cover">Cover</option><option value="video">Video</option></select></label><label class="text-sm">Title<input v-model="title" :disabled="locked" maxlength="150" placeholder="Optional photo title" class="mt-1 block w-full min-w-0 rounded border bg-card p-2"/></label></div>
      <FilePicker :model-value="files" multiple :accept="accept" :disabled="locked" label="Choose media files" @update:model-value="files = Array.isArray($event) ? $event : $event ? [$event] : []"/>
      <p v-if="progress !== null" role="status" class="text-sm text-muted-foreground">{{progress < 100 ? 'Uploading ' + progress + '%' : 'Processing uploaded files…'}}</p>
      <Button type="button" :disabled="locked || !files.length" @click="upload">{{busy ? 'Saving…' : 'Upload media'}}</Button>
    </div>
    <Dialog v-model:open="open"><DialogContent class="max-h-[90dvh] w-[calc(100vw_-_2rem)] overflow-y-auto sm:max-w-4xl">
      <DialogHeader><DialogTitle class="break-words">{{preview?.title || 'Media preview'}}</DialogTitle><DialogDescription class="capitalize">{{preview?.role || 'Gallery'}} · Uploaded media</DialogDescription></DialogHeader>
      <video v-if="preview?.role === 'video'" :src="mediaUrl(preview.url)" controls class="max-h-[65vh] w-full"/>
      <a v-else-if="preview && isPdf(preview)" :href="mediaUrl(preview.url)" download class="underline">Download document</a>
      <img v-else-if="preview && !failed[preview.url]" :src="mediaUrl(preview.url)" :alt="preview.title || 'Image preview'" class="max-h-[65vh] w-full object-contain" @error="failed[preview.url] = true"/>
      <p v-else class="p-8 text-center text-muted-foreground">This image could not be loaded.</p>
    </DialogContent></Dialog>
  </section>
</template>
