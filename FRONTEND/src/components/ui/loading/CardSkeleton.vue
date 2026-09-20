<script setup lang="ts">
import { Card, CardHeader, CardContent } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'

interface Props {
  cards?: number
  hasHeader?: boolean
}

withDefaults(defineProps<Props>(), {
  cards: 3,
  hasHeader: true
})
</script>

<template>
  <div class="space-y-4 animate-fade-in">
    <Card v-for="i in cards" :key="i">
      <CardHeader v-if="hasHeader">
        <Skeleton class="h-5 w-40" />
      </CardHeader>
      <CardContent>
        <div class="space-y-3">
          <Skeleton class="h-4 w-full" />
          <Skeleton class="h-4 w-3/4" />
          <Skeleton class="h-4 w-5/6" />
        </div>
      </CardContent>
    </Card>
  </div>
</template>
