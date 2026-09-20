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
  ArrowUpRight,
  ArrowRight,
  CalendarDays,
  Compass,
  MapPin,
  Hotel,
  Car,
  FileText,
  Users,
  MessageSquare,
  TrendingUp,
  Clock3,
  CheckCircle2,
  CircleDollarSign,
  Plane,
  Plus,
  Eye,
} from 'lucide-vue-next'
import api, { errorMessage } from '@/axiosClient'
import { user } from '@/composables/useAuth'

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

const quickActions = [
  {
    title: 'New Trip',
    description: 'Create a new travel package',
    icon: Compass,
    href: '/trips/create',
  },
  {
    title: 'New Inquiry',
    description: 'Record customer inquiry',
    icon: MessageSquare,
    href: '/inquiries/create',
  },
  {
    title: 'New Quotation',
    description: 'Prepare customer quotation',
    icon: FileText,
    href: '/quotations/create',
  },
  {
    title: 'Add Destination',
    description: 'Create travel destination',
    icon: MapPin,
    href: '/destinations/create',
  },
]
</script>

<template>
  <MainLayout title="Dashboard">

    <div class="space-y-6">

      <!-- Header -->
      <div
          class="relative overflow-hidden rounded-2xl border bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 px-6 py-7 text-white shadow-sm"
      >
        <div class="relative z-10">
          <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <div>
              <div class="mb-2 flex items-center gap-2 text-sm text-slate-300">
                <CalendarDays class="h-4 w-4"/>
                <span>Travel Operations</span>
              </div>

              <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                Welcome back, {{ user?.name }}
              </h1>

              <p class="mt-2 max-w-xl text-sm text-slate-300">
                Manage your trips, destinations, inquiries,
                accommodations and quotations from one place.
              </p>
            </div>

            <div class="flex flex-wrap gap-2">
              <RouterLink to="/inquiries/create">
                <Button class="bg-white text-slate-900 hover:bg-slate-100">
                  <Plus class="mr-2 h-4 w-4"/>
                  New Inquiry
                </Button>
              </RouterLink>

              <RouterLink to="/trips/list">
                <Button
                    variant="outline"
                    class="border-slate-600 bg-transparent text-white hover:bg-white/10 hover:text-white"
                >
                  View Trips
                  <ArrowUpRight class="ml-2 h-4 w-4"/>
                </Button>
              </RouterLink>
            </div>
          </div>
        </div>

        <!-- Decorative elements -->
        <div
            class="absolute -right-10 -top-16 h-48 w-48 rounded-full bg-white/5"
        />

        <div
            class="absolute -bottom-24 right-24 h-56 w-56 rounded-full bg-white/5"
        />

        <Plane
            class="absolute bottom-6 right-8 h-16 w-16 rotate-[-15deg] text-white/5 sm:h-24 sm:w-24"
        />
      </div>


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
          class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6"
      >
        <RouterLink
            v-for="item in stats"
            :key="item.key"
            :to="item.href"
            class="group"
        >
          <Card
              class="h-full transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md"
          >
            <CardContent class="p-5">

              <div class="flex items-start justify-between">
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl"
                    :class="item.bgClass"
                >
                  <component
                      :is="item.icon"
                      class="h-5 w-5"
                      :class="item.iconClass"
                  />
                </div>

                <ArrowUpRight
                    class="h-4 w-4 text-muted-foreground opacity-0 transition-opacity group-hover:opacity-100"
                />
              </div>

              <div class="mt-4">
                <p class="text-sm text-muted-foreground">
                  {{ item.title }}
                </p>

                <p class="mt-1 text-2xl font-bold tracking-tight">
                  {{ item.value.toLocaleString() }}
                </p>

                <p class="mt-1 text-xs text-muted-foreground">
                  {{ item.description }}
                </p>
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

            <div class="grid gap-4 sm:grid-cols-2">

              <!-- Inquiry -->
              <RouterLink
                  to="/inquiries/list"
                  class="group rounded-xl border p-4 transition hover:bg-muted/50"
              >
                <div class="flex items-center justify-between">
                  <div
                      class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950/30"
                  >
                    <MessageSquare class="h-5 w-5 text-blue-600"/>
                  </div>

                  <ArrowRight
                      class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1"
                  />
                </div>

                <p class="mt-4 text-sm font-medium">
                  Customer Inquiries
                </p>

                <p class="mt-1 text-2xl font-bold">
                  {{ getCount('inquiries').toLocaleString() }}
                </p>

                <p class="mt-1 text-xs text-muted-foreground">
                  Manage customer requests
                </p>
              </RouterLink>


              <!-- Trips -->
              <RouterLink
                  to="/trips/list"
                  class="group rounded-xl border p-4 transition hover:bg-muted/50"
              >
                <div class="flex items-center justify-between">
                  <div
                      class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950/30"
                  >
                    <Compass class="h-5 w-5 text-emerald-600"/>
                  </div>

                  <ArrowRight
                      class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1"
                  />
                </div>

                <p class="mt-4 text-sm font-medium">
                  Travel Packages
                </p>

                <p class="mt-1 text-2xl font-bold">
                  {{ getCount('trips').toLocaleString() }}
                </p>

                <p class="mt-1 text-xs text-muted-foreground">
                  Active trips and packages
                </p>
              </RouterLink>


              <!-- Quotations -->
              <RouterLink
                  to="/quotations/list"
                  class="group rounded-xl border p-4 transition hover:bg-muted/50"
              >
                <div class="flex items-center justify-between">
                  <div
                      class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-50 dark:bg-rose-950/30"
                  >
                    <FileText class="h-5 w-5 text-rose-600"/>
                  </div>

                  <ArrowRight
                      class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1"
                  />
                </div>

                <p class="mt-4 text-sm font-medium">
                  Quotations
                </p>

                <p class="mt-1 text-2xl font-bold">
                  {{ getCount('quotations').toLocaleString() }}
                </p>

                <p class="mt-1 text-xs text-muted-foreground">
                  Customer quotations
                </p>
              </RouterLink>


              <!-- Destinations -->
              <RouterLink
                  to="/destinations/list"
                  class="group rounded-xl border p-4 transition hover:bg-muted/50"
              >
                <div class="flex items-center justify-between">
                  <div
                      class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-950/30"
                  >
                    <MapPin class="h-5 w-5 text-orange-600"/>
                  </div>

                  <ArrowRight
                      class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1"
                  />
                </div>

                <p class="mt-4 text-sm font-medium">
                  Destinations
                </p>

                <p class="mt-1 text-2xl font-bold">
                  {{ getCount('destinations').toLocaleString() }}
                </p>

                <p class="mt-1 text-xs text-muted-foreground">
                  Places available for travel
                </p>
              </RouterLink>

            </div>

          </CardContent>
        </Card>


        <!-- Quick actions -->
        <Card>
          <CardHeader>
            <CardTitle>Quick Actions</CardTitle>
            <p class="text-sm text-muted-foreground">
              Frequently used operations
            </p>
          </CardHeader>

          <CardContent class="space-y-2">

            <RouterLink
                v-for="action in quickActions"
                :key="action.title"
                :to="action.href"
                class="group flex items-center gap-3 rounded-xl border p-3 transition hover:bg-muted/50"
            >
              <div
                  class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-muted"
              >
                <component
                    :is="action.icon"
                    class="h-4 w-4"
                />
              </div>

              <div class="min-w-0 flex-1">
                <p class="text-sm font-medium">
                  {{ action.title }}
                </p>

                <p class="truncate text-xs text-muted-foreground">
                  {{ action.description }}
                </p>
              </div>

              <ArrowRight
                  class="h-4 w-4 text-muted-foreground transition-transform group-hover:translate-x-1"
              />
            </RouterLink>

          </CardContent>
        </Card>

      </div>


      <!-- Bottom section -->
      <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

        <!-- Upcoming -->
        <Card>
          <CardHeader>
            <div class="flex items-center justify-between">
              <CardTitle>Upcoming Trips</CardTitle>
              <Clock3 class="h-5 w-5 text-muted-foreground"/>
            </div>
          </CardHeader>

          <CardContent>
            <div class="flex flex-col items-center justify-center py-8 text-center">
              <div
                  class="flex h-12 w-12 items-center justify-center rounded-full bg-muted"
              >
                <CalendarDays class="h-5 w-5 text-muted-foreground"/>
              </div>

              <p class="mt-3 text-sm font-medium">
                No upcoming trips
              </p>

              <p class="mt-1 max-w-xs text-xs text-muted-foreground">
                Upcoming scheduled trips will appear here.
              </p>

              <RouterLink
                  to="/trips/list"
                  class="mt-4"
              >
                <Button variant="outline" size="sm">
                  View trips
                  <ArrowRight class="ml-2 h-4 w-4"/>
                </Button>
              </RouterLink>
            </div>
          </CardContent>
        </Card>


        <!-- Recent activity -->
        <Card>
          <CardHeader>
            <div class="flex items-center justify-between">
              <CardTitle>Recent Activity</CardTitle>
              <Eye class="h-5 w-5 text-muted-foreground"/>
            </div>
          </CardHeader>

          <CardContent>
            <div class="space-y-5">

              <div class="flex gap-3">
                <div
                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-50 dark:bg-emerald-950/30"
                >
                  <CheckCircle2 class="h-4 w-4 text-emerald-600"/>
                </div>

                <div>
                  <p class="text-sm font-medium">
                    System is ready
                  </p>

                  <p class="text-xs text-muted-foreground">
                    Your travel operations dashboard is active.
                  </p>
                </div>
              </div>

              <div class="flex gap-3">
                <div
                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 dark:bg-blue-950/30"
                >
                  <Users class="h-4 w-4 text-blue-600"/>
                </div>

                <div>
                  <p class="text-sm font-medium">
                    Customer management
                  </p>

                  <p class="text-xs text-muted-foreground">
                    Manage inquiries and customer requests.
                  </p>
                </div>
              </div>

              <div class="flex gap-3">
                <div
                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-orange-50 dark:bg-orange-950/30"
                >
                  <CircleDollarSign class="h-4 w-4 text-orange-600"/>
                </div>

                <div>
                  <p class="text-sm font-medium">
                    Quotations
                  </p>

                  <p class="text-xs text-muted-foreground">
                    Prepare and manage customer quotations.
                  </p>
                </div>
              </div>

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