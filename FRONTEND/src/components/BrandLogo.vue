<script setup lang="ts">
import { brand } from '@/brand'
</script>

<template>
  <!-- Show the wordmark within the supplied PNG's teal margins; preserve the original file. -->
  <span class="relative block aspect-[442/176] overflow-hidden rounded-md bg-[#288479]">
    <img :src="brand.logo" :alt="brand.name" width="711" height="490"
      class="absolute max-w-none" style="width:160.86%;height:auto;left:-30.32%;top:-89.77%" />
  </span>
</template>
