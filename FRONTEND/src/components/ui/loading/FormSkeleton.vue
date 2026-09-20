<script setup lang="ts">
import { Card, CardHeader, CardContent } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'

interface Section {
  name?: string
  fields: number
  columns?: 1 | 2
}

interface Props {
  sections: Section[]
}

defineProps<Props>()
</script>

<template>
  <div class="space-y-6 animate-fade-in">
    <Card v-for="(section, idx) in sections" :key="idx" class="border-l-4 border-l-muted">
      <CardHeader v-if="section.name" class="px-4 pb-3">
        <Skeleton class="h-5 w-32" />
      </CardHeader>
      <CardContent class="px-4 pb-4">
        <div :class="section.columns === 2 ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'space-y-6'">
          <div v-for="i in section.fields" :key="i" class="space-y-2">
            <Skeleton class="h-4 w-24" />
            <Skeleton class="h-10 w-full" />
          </div>
        </div>
      </CardContent>
    </Card>
    <div class="flex gap-3 justify-end">
      <Skeleton class="h-11 w-24" />
      <Skeleton class="h-11 w-24" />
    </div>
  </div>
</template>
