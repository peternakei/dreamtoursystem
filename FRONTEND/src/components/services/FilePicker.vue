<script setup lang="ts">
import {computed, onBeforeUnmount, ref, useId, watch} from 'vue'
import {FileText, ImagePlus, X, Eye} from 'lucide-vue-next'
import {Button} from '@/components/ui/button'
import {Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription} from '@/components/ui/dialog'

const props = withDefaults(defineProps<{
  modelValue: File | File[] | null | undefined
  id?: string
  label?: string
  accept?: string
  multiple?: boolean
  required?: boolean
  disabled?: boolean
}>(), {label: 'Choose files', accept: '', multiple: false})
const emit = defineEmits<{'update:modelValue': [value: File | File[] | null]}>()
const generatedId = useId()
const input = ref<HTMLInputElement | null>(null)
const dragging = ref(false)
const previews = ref<{file: File; url: string}[]>([])
const selected = ref<{file: File; url: string} | null>(null)
const open = ref(false)
const files = computed(() => Array.isArray(props.modelValue) ? props.modelValue : props.modelValue ? [props.modelValue] : [])
const isImage = (file: File) => file.type.startsWith('image/')
const isVideo = (file: File) => file.type.startsWith('video/')
const size = (bytes: number) => bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB'

function release() { previews.value.forEach(item => { if (item.url) URL.revokeObjectURL(item.url) }) }
watch(files, value => {
  open.value = false
  selected.value = null
  release()
  previews.value = value.map(file => ({file, url: isImage(file) || isVideo(file) ? URL.createObjectURL(file) : ''}))
  if (!value.length && input.value) input.value.value = ''
}, {immediate: true})
onBeforeUnmount(release)

function choose(incoming: File[]) {
  if (props.disabled || !incoming.length) return
  const combined = props.multiple ? [...files.value, ...incoming] : incoming.slice(0, 1)
  const unique = combined.filter((file, index) => combined.findIndex(other => other.name === file.name && other.size === file.size && other.lastModified === file.lastModified) === index)
  emit('update:modelValue', props.multiple ? unique : unique[0])
}
function changed(event: Event) {
  const target = event.target as HTMLInputElement
  choose(Array.from(target.files || []))
  target.value = ''
}
function drop(event: DragEvent) {
  dragging.value = false
  choose(Array.from(event.dataTransfer?.files || []))
}
function remove(index: number) {
  const remaining = files.value.filter((_, i) => i !== index)
  emit('update:modelValue', props.multiple ? remaining : remaining[0] || null)
}
function preview(item: {file: File; url: string}) { selected.value = item; open.value = true }
</script>

<template>
  <div class="min-w-0 space-y-3">
    <div class="space-y-3 rounded-lg border-2 border-dashed p-4 transition-colors"
         :class="[dragging ? 'border-primary bg-primary/5' : 'border-border', disabled ? 'opacity-60' : '']"
         @dragover.prevent="!disabled && (dragging = true)" @dragleave.prevent="dragging = false" @drop.prevent="drop">
      <div class="flex items-center gap-3"><ImagePlus class="h-7 w-7 shrink-0 text-muted-foreground"/><p class="text-sm">Choose {{multiple ? 'files' : 'a file'}} or drop {{multiple ? 'them' : 'it'}} here. Photos appear below before you upload.</p></div>
      <input :id="id || generatedId" ref="input" type="file" :aria-label="label" :accept="accept" :multiple="multiple"
             :required="required && !files.length" :disabled="disabled" class="block w-full min-w-0 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-secondary file:px-3 file:py-2 file:text-secondary-foreground" @change="changed"/>
    </div>
    <div v-if="previews.length" class="grid gap-3 sm:grid-cols-2" aria-label="Selected files">
      <article v-for="(item, index) in previews" :key="item.url || item.file.name + index" class="min-w-0 overflow-hidden rounded-lg border bg-card text-card-foreground">
        <button v-if="item.url" type="button" class="relative block aspect-[4/3] w-full bg-muted/40" :aria-label="'Preview selected ' + item.file.name" @click="preview(item)">
          <img v-if="isImage(item.file)" :src="item.url" :alt="item.file.name" class="h-full w-full object-contain p-2"/>
          <video v-else :src="item.url" preload="metadata" class="h-full w-full object-contain"/>
          <span class="absolute bottom-2 right-2 rounded bg-card/90 p-1.5"><Eye class="h-4 w-4"/></span>
        </button>
        <div v-else class="flex h-20 items-center justify-center bg-muted/40"><FileText class="h-8 w-8 text-muted-foreground"/></div>
        <div class="flex items-center gap-2 p-3"><div class="min-w-0 flex-1"><p class="truncate text-sm font-medium" :title="item.file.name">{{item.file.name}}</p><p class="text-xs text-muted-foreground">{{size(item.file.size)}} · Ready to upload</p></div><Button type="button" variant="ghost" size="icon" :disabled="disabled" :aria-label="'Remove selected ' + item.file.name" @click="remove(index)"><X class="h-4 w-4"/></Button></div>
      </article>
    </div>
    <Dialog v-model:open="open"><DialogContent class="max-h-[90dvh] w-[calc(100vw_-_2rem)] overflow-y-auto sm:max-w-4xl">
      <DialogHeader><DialogTitle class="break-all">{{selected?.file.name || 'File preview'}}</DialogTitle><DialogDescription>Selected file · Not uploaded yet</DialogDescription></DialogHeader>
      <img v-if="selected && isImage(selected.file)" :src="selected.url" :alt="selected.file.name" class="max-h-[65vh] w-full object-contain"/>
      <video v-else-if="selected && isVideo(selected.file)" :src="selected.url" controls class="max-h-[65vh] w-full"/>
    </DialogContent></Dialog>
  </div>
</template>
