<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/lib/utils'
import { statusBadgeVariants } from '@/styles/colors'

interface Props {
  status: string  // 'active', 'inactive', 'pending', etc.
  label: string
  size?: 'sm' | 'md' | 'lg'
  variant?: 'default' | 'outline'
  showIcon?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  variant: 'outline',
  showIcon: true
})

const statusKey = computed(() => props.status.toLowerCase())
const colorClasses = computed(() => statusBadgeVariants[statusKey.value as keyof typeof statusBadgeVariants] || statusBadgeVariants.inactive)

const sizeClasses = {
  sm: 'px-1.5 py-0.5 text-xs',
  md: 'px-2 py-0.5 text-xs',
  lg: 'px-2.5 py-1 text-sm'
}
</script>

<template>
  <span
    role="status"
    :class="cn(
      sizeClasses[size],
      'inline-flex items-center gap-1.5 font-medium rounded-md border transition-standard',
      colorClasses.bg,
      colorClasses.text,
      colorClasses.border
    )">
    <span
      v-if="showIcon"
      class="h-1.5 w-1.5 rounded-full"
      :class="colorClasses.icon"
      aria-hidden="true"
    />
    {{ label }}
  </span>
</template>
