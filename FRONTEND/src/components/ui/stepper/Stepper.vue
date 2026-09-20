<script setup lang="ts">
import { Check } from 'lucide-vue-next'

interface Step {
  id: string
  label: string
  description?: string
  icon?: any
}

const props = withDefaults(defineProps<{
  steps: Step[]
  currentStep: number
  completedSteps?: number[]
  strictNavigation?: boolean
}>(), {
  strictNavigation: false
})

const emit = defineEmits<{
  'step-click': [index: number]
}>()

const isStepCompleted = (index: number) => {
  return props.completedSteps?.includes(index) || index < props.currentStep
}

const isStepActive = (index: number) => {
  return index === props.currentStep
}

const handleStepClick = (index: number) => {
  if (props.strictNavigation) {
    if (index <= props.currentStep || props.completedSteps?.includes(index)) {
      emit('step-click', index)
    }
  } else {
    emit('step-click', index)
  }
}

const isStepClickable = (index: number) => {
  if (!props.strictNavigation) return true
  return index <= props.currentStep || props.completedSteps?.includes(index)
}
</script>

<template>
  <div class="w-full">
    <!-- Desktop Stepper -->
    <div class="hidden md:flex items-center overflow-x-auto pb-2 mb-3 scrollbar-thin">
      <div
        v-for="(step, index) in steps"
        :key="step.id"
        class="flex items-center flex-shrink-0"
      >
        <div
          :class="[
            'flex items-center',
            isStepClickable(index) ? 'cursor-pointer' : 'cursor-not-allowed opacity-50'
          ]"
          @click="handleStepClick(index)"
        >
          <!-- Step Circle -->
          <div
            :class="[
              'flex items-center justify-center w-7 h-7 rounded-full border-2 transition-all flex-shrink-0',
              isStepCompleted(index)
                ? 'bg-primary border-primary text-primary-foreground'
                : isStepActive(index)
                ? 'bg-primary/20 border-primary text-primary'
                : 'bg-muted border-border text-muted-foreground'
            ]"
          >
            <Check v-if="isStepCompleted(index)" class="h-3.5 w-3.5" />
            <component
              v-else-if="step.icon"
              :is="step.icon"
              class="h-3.5 w-3.5"
            />
            <span v-else class="text-xs font-semibold">{{ index + 1 }}</span>
          </div>

          <!-- Step Label -->
          <div class="ml-2 max-w-[140px]">
            <div
              :class="[
                'text-xs font-medium truncate',
                isStepActive(index)
                  ? 'text-primary'
                  : isStepCompleted(index)
                  ? 'text-foreground'
                  : 'text-muted-foreground'
              ]"
              :title="step.label"
            >
              {{ step.label }}
            </div>
            <div
              v-if="step.description"
              :class="[
                'text-[10px] mt-0.5 truncate',
                isStepActive(index)
                  ? 'text-primary/80'
                  : 'text-muted-foreground'
              ]"
              :title="step.description"
            >
              {{ step.description }}
            </div>
          </div>
        </div>

        <!-- Connector Line -->
        <div
          v-if="index < steps.length - 1"
          :class="[
            'w-8 mx-3 h-0.5 transition-all flex-shrink-0',
            isStepCompleted(index) ? 'bg-primary' : 'bg-border'
          ]"
        />
      </div>
    </div>

    <!-- Mobile Stepper -->
    <div class="md:hidden mb-3">
      <div class="flex items-center justify-between mb-1.5">
        <span class="text-[10px] text-muted-foreground">
          Step {{ currentStep + 1 }} of {{ steps.length }}
        </span>
        <span class="text-[10px] text-muted-foreground">
          {{ Math.round(((currentStep + 1) / steps.length) * 100) }}%
        </span>
      </div>
      <div class="w-full bg-muted rounded-full h-1.5">
        <div
          class="bg-primary h-1.5 rounded-full transition-all duration-300"
          :style="{ width: `${((currentStep + 1) / steps.length) * 100}%` }"
        />
      </div>
      <div class="mt-1.5 text-xs font-medium text-foreground truncate">
        {{ steps[currentStep]?.label }}
      </div>
      <div v-if="steps[currentStep]?.description" class="mt-0.5 text-[10px] text-muted-foreground truncate">
        {{ steps[currentStep]?.description }}
      </div>
    </div>
  </div>
</template>
