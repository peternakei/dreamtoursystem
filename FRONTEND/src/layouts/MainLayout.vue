<script setup lang="ts">
import {brand} from '@/brand'
import BrandLogo from '@/components/BrandLogo.vue'
import {useRouter} from 'vue-router'
import {Compass, LogOut} from 'lucide-vue-next'
import SidebarNavigation from '@/components/SidebarNavigation.vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarInset,
  SidebarProvider,
  SidebarTrigger
} from '@/components/ui/sidebar'
import ThemeToggle from '@/components/ui/ThemeToggle.vue'
import {Button} from '@/components/ui/button'
import {user, logout} from '@/composables/useAuth'
import {errorMessage} from '@/axiosClient'
import {toast} from 'vue-sonner'

defineProps<{ title: string }>()
const router = useRouter()

async function signOut() {
  try {
    await logout();
    await router.push('/login')
  } catch (e) {
    toast.error(errorMessage(e))
  }
}
</script>
<template>
  <SidebarProvider>
    <Sidebar collapsible="icon">
      <SidebarHeader class="border-b border-sidebar-border bg-sidebar ">
        <RouterLink to="/dashboard" :aria-label="brand.name"
                    class="flex flex-col gap-2 p-2 group-data-[collapsible=icon]:p-0">
          <BrandLogo class="w-full group-data-[collapsible=icon]:hidden"/>
          <Compass class="mx-auto hidden h-7 w-7 text-primary group-data-[collapsible=icon]:block" aria-hidden="true"/>
<!--          <span class="text-xs text-end text-white p-1 fw-bolder group-data-[collapsible=icon]:hidden">TRAVEL MANAGEMENT SYSTEM</span>-->
        </RouterLink>
      </SidebarHeader>
      <SidebarContent>
        <SidebarNavigation />
      </SidebarContent>
      <SidebarFooter class="border-t">
        <p class="px-2 text-xs text-sidebar-foreground group-data-[collapsible=icon]:hidden">{{ brand.name }}</p>
      </SidebarFooter>
    </Sidebar>
    <SidebarInset class="min-w-0">
      <header class="sticky top-0 z-20 flex h-16 items-center gap-3 border-b bg-background/95 px-5 backdrop-blur">
        <SidebarTrigger/>
        <h1 class="text-base font-semibold">{{ title }}</h1>
        <div class="ml-auto flex items-center gap-3">
          <span class="hidden text-sm text-muted-foreground sm:block">{{ user?.name }}</span>
          <ThemeToggle/>
          <Button variant="ghost" size="icon" @click="signOut" aria-label="Log out">
            <LogOut class="h-4 w-4"/>
          </Button>
        </div>
      </header>
      <main class="min-h-[calc(100vh-8rem)] bg-background p-4 md:p-6">
        <slot/>
      </main>
      <footer class="brand-footer border-t p-4 text-center text-xs">© {{ new Date().getFullYear() }} {{ brand.name }}
      </footer>
    </SidebarInset>
  </SidebarProvider>
</template>
