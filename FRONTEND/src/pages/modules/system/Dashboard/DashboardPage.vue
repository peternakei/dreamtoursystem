<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import MainLayout from '@/layouts/MainLayout.vue'
import {
  Card,
  CardContent,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import {
  ArrowRight,
  Compass,
  MapPin,
  Hotel,
  Car,
  FileText,
  MessageSquare,
  TrendingUp,
} from 'lucide-vue-next'
import api, { errorMessage } from '@/axiosClient'

const counts = ref<Record<string, number>>({})
const error = ref('')
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await api.get('/workspace/dashboard')

    counts.value = response.data.counts ?? {}
  } catch (e) {
    error.value = errorMessage(e)
  } finally {
    loading.value = false
  }
})

const getCount = (key: string) => counts.value[key] ?? 0

const stats = computed(() => [
  {
    key: 'inquiries',
    title: 'Inquiries',
    value: getCount('inquiries'),
    description: 'Customer inquiries',
    icon: MessageSquare,
    iconClass: 'text-blue-600',
    bgClass: 'bg-blue-50 dark:bg-blue-950/30',
    href: '/inquiries/list',
  },
  {
    key: 'trips',
    title: 'Trips',
    value: getCount('trips'),
    description: 'Available trips',
    icon: Compass,
    iconClass: 'text-emerald-600',
    bgClass: 'bg-emerald-50 dark:bg-emerald-950/30',
    href: '/trips/list',
  },
  {
    key: 'destinations',
    title: 'Destinations',
    value: getCount('destinations'),
    description: 'Travel destinations',
    icon: MapPin,
    iconClass: 'text-orange-600',
    bgClass: 'bg-orange-50 dark:bg-orange-950/30',
    href: '/destinations/list',
  },
  {
    key: 'accommodations',
    title: 'Accommodations',
    value: getCount('accommodations'),
    description: 'Hotels & lodges',
    icon: Hotel,
    iconClass: 'text-purple-600',
    bgClass: 'bg-purple-50 dark:bg-purple-950/30',
    href: '/accommodations/list',
  },
  {
    key: 'vehicles',
    title: 'Vehicles',
    value: getCount('vehicles'),
    description: 'Available vehicles',
    icon: Car,
    iconClass: 'text-cyan-600',
    bgClass: 'bg-cyan-50 dark:bg-cyan-950/30',
    href: '/vehicles/list',
  },
  {
    key: 'quotations',
    title: 'Quotations',
    value: getCount('quotations'),
    description: 'Customer quotations',
    icon: FileText,
    iconClass: 'text-rose-600',
    bgClass: 'bg-rose-50 dark:bg-rose-950/30',
    href: '/quotations/list',
  },
])

</script>

<template>
  <MainLayout title="Dashboard">

    <div class="space-y-6">

      <!-- Header -->

      <!-- Error -->
      <div
          v-if="error"
          role="alert"
          class="rounded-lg border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm text-destructive"
      >
        {{ error }}
      </div>


      <!-- Loading -->
      <div
          v-if="loading"
          class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
      >
        <Card
            v-for="i in 6"
            :key="i"
            class="animate-pulse"
        >
          <CardContent class="p-5">
            <div class="h-10 w-10 rounded-lg bg-muted"/>
            <div class="mt-4 h-4 w-24 rounded bg-muted"/>
            <div class="mt-2 h-8 w-16 rounded bg-muted"/>
          </CardContent>
        </Card>
      </div>


      <!-- Statistics -->
      <div
          v-if="!loading"
          class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
      >
        <RouterLink
            v-for="item in stats"
            :key="item.key"
            :to="item.href"
            class="group min-w-0"
        >
          <Card
              class="h-full border transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md"
          >
            <CardContent class="p-4">

              <div class="flex items-center gap-3">

                <!-- Icon -->
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                    :class="item.bgClass"
                >
                  <component
                      :is="item.icon"
                      class="h-5 w-5"
                      :class="item.iconClass"
                  />
                </div>

                <!-- Title + description -->
                <div class="min-w-0 flex-1">
                  <p
                      class="truncate text-xs font-medium text-muted-foreground"
                  >
                    {{ item.title }}
                  </p>

                  <p
                      class="mt-0.5 truncate text-xs text-muted-foreground/70"
                  >
                    {{ item.description }}
                  </p>
                </div>

                <!-- Number -->
                <div class="shrink-0 text-right">
                  <p class="text-xl font-bold tracking-tight">
                    {{ item.value.toLocaleString() }}
                  </p>
                </div>

              </div>

            </CardContent>
          </Card>
        </RouterLink>
      </div>


      <!-- Main content -->
      <div class="grid gap-6 lg:grid-cols-3">

        <!-- Operations overview -->
        <Card class="lg:col-span-2">
          <CardHeader class="flex flex-row items-center justify-between">
            <div>
              <CardTitle>Operations Overview</CardTitle>
              <p class="mt-1 text-sm text-muted-foreground">
                Your travel business at a glance
              </p>
            </div>

            <TrendingUp class="h-5 w-5 text-emerald-600"/>
          </CardHeader>

          <CardContent>
            <div class="grid gap-3 sm:grid-cols-2">

              <!-- Inquiry -->
              <RouterLink
                  to="/inquiries/list"
                  class="group rounded-xl border bg-card p-4 transition-all hover:border-blue-200 hover:bg-muted/40 hover:shadow-sm"
              >
                <div class="flex items-center gap-4">

                  <!-- Icon -->
                  <div
                      class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-950/30"
                  >
                    <MessageSquare class="h-5 w-5 text-blue-600"/>
                  </div>

                  <!-- Details -->
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">
                      Customer Inquiries
                    </p>

                    <p class="mt-1 text-xs text-muted-foreground">
                      Customer requests
                    </p>
                  </div>

                  <!-- Number + Arrow -->
                  <div class="flex items-center gap-3">
                    <p class="text-2xl font-bold tracking-tight">
                      {{ getCount('inquiries').toLocaleString() }}
                    </p>

                    <ArrowRight
                        class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1"
                    />
                  </div>

                </div>
              </RouterLink>


              <!-- Trips -->
              <RouterLink
                  to="/trips/list"
                  class="group rounded-xl border bg-card p-4 transition-all hover:border-emerald-200 hover:bg-muted/40 hover:shadow-sm"
              >
                <div class="flex items-center gap-4">

                  <!-- Icon -->
                  <div
                      class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/30"
                  >
                    <Compass class="h-5 w-5 text-emerald-600"/>
                  </div>

                  <!-- Details -->
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">
                      Travel Packages
                    </p>

                    <p class="mt-1 text-xs text-muted-foreground">
                      Active trips
                    </p>
                  </div>

                  <!-- Number + Arrow -->
                  <div class="flex items-center gap-3">
                    <p class="text-2xl font-bold tracking-tight">
                      {{ getCount('trips').toLocaleString() }}
                    </p>

                    <ArrowRight
                        class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1"
                    />
                  </div>

                </div>
              </RouterLink>


              <!-- Quotations -->
              <RouterLink
                  to="/quotations/list"
                  class="group rounded-xl border bg-card p-4 transition-all hover:border-rose-200 hover:bg-muted/40 hover:shadow-sm"
              >
                <div class="flex items-center gap-4">

                  <!-- Icon -->
                  <div
                      class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-950/30"
                  >
                    <FileText class="h-5 w-5 text-rose-600"/>
                  </div>

                  <!-- Details -->
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">
                      Quotations
                    </p>

                    <p class="mt-1 text-xs text-muted-foreground">
                      Customer quotations
                    </p>
                  </div>

                  <!-- Number + Arrow -->
                  <div class="flex items-center gap-3">
                    <p class="text-2xl font-bold tracking-tight">
                      {{ getCount('quotations').toLocaleString() }}
                    </p>

                    <ArrowRight
                        class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1"
                    />
                  </div>

                </div>
              </RouterLink>


              <!-- Destinations -->
              <RouterLink
                  to="/destinations/list"
                  class="group rounded-xl border bg-card p-4 transition-all hover:border-orange-200 hover:bg-muted/40 hover:shadow-sm"
              >
                <div class="flex items-center gap-4">

                  <!-- Icon -->
                  <div
                      class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-950/30"
                  >
                    <MapPin class="h-5 w-5 text-orange-600"/>
                  </div>

                  <!-- Details -->
                  <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">
                      Destinations
                    </p>

                    <p class="mt-1 text-xs text-muted-foreground">
                      Travel locations
                    </p>
                  </div>

                  <!-- Number + Arrow -->
                  <div class="flex items-center gap-3">
                    <p class="text-2xl font-bold tracking-tight">
                      {{ getCount('destinations').toLocaleString() }}
                    </p>

                    <ArrowRight
                        class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1"
                    />
                  </div>

                </div>
              </RouterLink>

            </div>
          </CardContent>
        </Card>


        <!-- Resources -->
        <Card>
          <CardHeader>
            <CardTitle>Travel Resources</CardTitle>
            <p class="text-sm text-muted-foreground">
              Manage your travel inventory
            </p>
          </CardHeader>

          <CardContent class="space-y-3">

            <RouterLink
                to="/accommodations/list"
                class="flex items-center justify-between rounded-lg border p-3 transition hover:bg-muted/50"
            >
              <div class="flex items-center gap-3">
                <Hotel class="h-5 w-5 text-purple-600"/>
                <div>
                  <p class="text-sm font-medium">
                    Accommodations
                  </p>
                  <p class="text-xs text-muted-foreground">
                    {{ getCount('accommodations').toLocaleString() }} available
                  </p>
                </div>
              </div>

              <ArrowRight class="h-4 w-4 text-muted-foreground"/>
            </RouterLink>


            <RouterLink
                to="/vehicles/list"
                class="flex items-center justify-between rounded-lg border p-3 transition hover:bg-muted/50"
            >
              <div class="flex items-center gap-3">
                <Car class="h-5 w-5 text-cyan-600"/>
                <div>
                  <p class="text-sm font-medium">
                    Vehicles
                  </p>
                  <p class="text-xs text-muted-foreground">
                    {{ getCount('vehicles').toLocaleString() }} registered
                  </p>
                </div>
              </div>

              <ArrowRight class="h-4 w-4 text-muted-foreground"/>
            </RouterLink>


            <RouterLink
                to="/destinations/list"
                class="flex items-center justify-between rounded-lg border p-3 transition hover:bg-muted/50"
            >
              <div class="flex items-center gap-3">
                <MapPin class="h-5 w-5 text-orange-600"/>
                <div>
                  <p class="text-sm font-medium">
                    Destinations
                  </p>
                  <p class="text-xs text-muted-foreground">
                    {{ getCount('destinations').toLocaleString() }} locations
                  </p>
                </div>
              </div>

              <ArrowRight class="h-4 w-4 text-muted-foreground"/>
            </RouterLink>

          </CardContent>
        </Card>


      </div>


      <!-- Footer quick navigation -->
      <Card>
        <CardContent class="p-5">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
              <p class="font-medium">
                Plan your next journey
              </p>

              <p class="mt-1 text-sm text-muted-foreground">
                Quickly access the tools you use most.
              </p>
            </div>

            <div class="flex flex-wrap gap-2">

              <RouterLink to="/trips/list">
                <Button variant="outline" size="sm">
                  <Compass class="mr-2 h-4 w-4"/>
                  Trips
                </Button>
              </RouterLink>

              <RouterLink to="/destinations/list">
                <Button variant="outline" size="sm">
                  <MapPin class="mr-2 h-4 w-4"/>
                  Destinations
                </Button>
              </RouterLink>

              <RouterLink to="/accommodations/list">
                <Button variant="outline" size="sm">
                  <Hotel class="mr-2 h-4 w-4"/>
                  Hotels
                </Button>
              </RouterLink>

              <RouterLink to="/vehicles/list">
                <Button variant="outline" size="sm">
                  <Car class="mr-2 h-4 w-4"/>
                  Vehicles
                </Button>
              </RouterLink>

              <RouterLink to="/quotations/list">
                <Button variant="outline" size="sm">
                  <FileText class="mr-2 h-4 w-4"/>
                  Quotations
                </Button>
              </RouterLink>

            </div>
          </div>
        </CardContent>
      </Card>

    </div>
  </MainLayout>
</template>