<script setup lang="ts">
import { reactive, watch } from 'vue'
import { useRoute } from 'vue-router'
import { ChevronRight, LayoutDashboard } from 'lucide-vue-next'
import { navigation, type NavigationMenu } from '@/navigation'
import {
  SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem,
  SidebarMenuSub, SidebarMenuSubButton, SidebarMenuSubItem, useSidebar,
} from '@/components/ui/sidebar'

const route = useRoute()
const { state, isMobile, setOpen, setOpenMobile } = useSidebar()
const expanded = reactive<Record<string, boolean>>({})
const isActive = (slug: string) => route.path === '/' + slug || route.path.startsWith('/' + slug + '/')
const containsActive = (menu: NavigationMenu) => menu.items.some(item => isActive(item.slug))
const isExpanded = (id: string) => !!expanded[id] && (isMobile.value || state.value === 'expanded')

watch(() => route.path, () => {
  for (const section of navigation) {
    for (const menu of section.menus) {
      if (containsActive(menu)) expanded[menu.id] = true
    }
  }
}, { immediate: true })

function toggleMenu(id: string) {
  if (!isMobile.value && state.value === 'collapsed') {
    setOpen(true)
    expanded[id] = true
  } else {
    expanded[id] = !expanded[id]
  }
}

function followLink() {
  if (isMobile.value) setOpenMobile(false)
}
</script>

<template>
  <nav aria-label="Main navigation">
    <SidebarGroup>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton as-child :is-active="route.path === '/dashboard'" tooltip="Dashboard">
            <RouterLink to="/dashboard" :aria-current="route.path === '/dashboard' ? 'page' : undefined" @click="followLink">
              <LayoutDashboard aria-hidden="true" />
              <span>Dashboard</span>
            </RouterLink>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarGroup>
    <SidebarGroup v-for="section in navigation" :key="section.title">
      <SidebarGroupLabel class="text-[11px] font-semibold uppercase tracking-wider">{{ section.title }}</SidebarGroupLabel>
      <SidebarMenu>
        <SidebarMenuItem v-for="menu in section.menus" :key="menu.id">
          <SidebarMenuButton
            type="button"
            :tooltip="menu.title"
            :aria-label="menu.title"
            :aria-expanded="isExpanded(menu.id)"
            :aria-controls="'nav-' + menu.id"
            :data-menu="menu.id"
            :class="containsActive(menu) ? 'bg-sidebar-foreground/10 font-semibold' : ''"
            @click="toggleMenu(menu.id)"
          >
            <component :is="menu.icon" aria-hidden="true" />
            <span>{{ menu.title }}</span>
            <ChevronRight aria-hidden="true" class="ml-auto transition-transform duration-200 motion-reduce:transition-none"
              :class="{ 'rotate-90': isExpanded(menu.id) }" />
          </SidebarMenuButton>
          <SidebarMenuSub v-show="isExpanded(menu.id)" :id="'nav-' + menu.id" class="my-1">
            <SidebarMenuSubItem v-for="item in menu.items" :key="item.slug">
              <SidebarMenuSubButton as-child :is-active="isActive(item.slug)" class="h-9">
                <RouterLink :to="'/' + item.slug + '/list'" :aria-current="isActive(item.slug) ? 'page' : undefined" @click="followLink">
                  <span>{{ item.title }}</span>
                </RouterLink>
              </SidebarMenuSubButton>
            </SidebarMenuSubItem>
          </SidebarMenuSub>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarGroup>


  </nav>
</template>
