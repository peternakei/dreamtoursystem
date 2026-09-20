<script setup lang="ts">
import { cn } from '@/lib/utils'
import { Check, Minus } from 'lucide-vue-next'
import { type HTMLAttributes } from 'vue'

interface Props {
  id?: string
  class?: HTMLAttributes['class']
  indeterminate?: boolean
  modelValue?: boolean
  disabled?: boolean
}

const props = defineProps<Props>()
const emits = defineEmits<{ 'update:modelValue': [value: boolean] }>()

const handleClick = () => {
  if (!props.disabled) {
    emits('update:modelValue', !props.modelValue)
  }
}
</script>

<template>
  <button
    :id="id"
    type="button"
    role="checkbox"
    :aria-checked="modelValue"
    :aria-label="id"
    :disabled="disabled"
    :class="
      cn('peer size-4 shrink-0 rounded-[4px] border shadow-xs transition-all outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50',
         // Base styling
         'border-input',
         // Focus and error states
         'focus-visible:border-ring focus-visible:ring-ring/50',
         // State-based styling
         (modelValue || indeterminate) ? 'bg-black border-black' : 'bg-background',
         props.class)"
    @click="handleClick"
  >
    <div class="flex items-center justify-center w-full h-full">
      <Check v-if="modelValue && !indeterminate" class="size-3.5 text-white" />
      <Minus v-else-if="indeterminate" class="w-full text-white" />
    </div>
  </button>
</template> 