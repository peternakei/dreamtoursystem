<script setup lang="ts">
import {onMounted,ref} from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'
import {Card,CardContent,CardHeader,CardTitle} from '@/components/ui/card'
import {Button} from '@/components/ui/button'
import {ArrowUpRight,Compass} from 'lucide-vue-next'
import api,{errorMessage} from '@/axiosClient'
import {user} from '@/composables/useAuth'
const counts=ref<Record<string,number>>({}),error=ref(''),loading=ref(true)
onMounted(async()=>{try{counts.value=(await api.get('/workspace/dashboard')).data.counts}catch(e){error.value=errorMessage(e)}finally{loading.value=false}})
</script>
<template>
<MainLayout title="Dashboard">
<div class="mb-7 flex flex-wrap items-end justify-between gap-4">
<div>
<p class="text-sm text-muted-foreground">Your operations at a glance</p>
<h2 class="mt-2 text-2xl font-semibold">Welcome, {{user?.name}}</h2>
</div>
<RouterLink to="inquiries/list">
<Button >View inquiries<ArrowUpRight class="h-4 w-4"/>
</Button>
</RouterLink>
</div>
<p v-if="error" role="alert" class="text-destructive">{{error}}</p>
<p v-if="loading" class="text-muted-foreground">Loading your dashboard…</p>
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
<RouterLink v-for="(count,key) in counts" :key="key" :to="'/'+key+'/list'">
<Card class="transition-shadow hover:shadow-md">
<CardHeader class="flex-row items-center justify-between pb-2">
<CardTitle class="text-sm font-medium capitalize text-muted-foreground">{{key}}</CardTitle>
<Compass class="h-4 w-4 text-brand-teal"/>
</CardHeader>
<CardContent>
<p class="text-3xl font-semibold">{{count.toLocaleString()}}</p>
<p class="mt-2 text-xs text-muted-foreground">View all {{key}} →</p>
</CardContent>
</Card>
</RouterLink>
</div>
<Card class="mt-6">
<CardHeader>
<CardTitle>Plan the next journey</CardTitle>
</CardHeader>
<CardContent class="flex flex-wrap gap-3">
<RouterLink v-for="item in ['trips','destinations','accommodations','vehicles','quotations']" :key="item" :to="'/'+item+'/list'">
<Button variant="outline" class="capitalize">{{item}}<ArrowUpRight class="h-4 w-4"/>
</Button>
</RouterLink>
</CardContent>
</Card>
</MainLayout>
</template>
