<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'

interface Props {
  rows?: number
  columns?: number
}

withDefaults(defineProps<Props>(), {
  rows: 5,
  columns: 4
})
</script>

<template>
  <div class="space-y-4 animate-fade-in">
    <div class="flex gap-2">
      <Skeleton class="h-10 flex-1" />
      <Skeleton class="h-10 w-32" />
    </div>
    <div class="border rounded-lg overflow-hidden">
      <div class="bg-muted p-4">
        <div class="flex gap-4">
          <Skeleton v-for="i in columns" :key="i" class="h-4 flex-1" />
        </div>
      </div>
      <div v-for="i in rows" :key="i" class="p-4 border-t">
        <div class="flex gap-4">
          <Skeleton v-for="j in columns" :key="j" class="h-4 flex-1" />
        </div>
      </div>
    </div>
  </div>
</template>
