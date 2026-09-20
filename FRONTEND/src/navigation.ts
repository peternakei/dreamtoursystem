import type {Component} from 'vue'
import {CalendarDays, Compass, Wallet, FileText, SlidersHorizontal, MapPin, ShieldCheck} from 'lucide-vue-next'
import modules from '@/modules.json'

export interface NavigationLink {
    slug: string;
    title: string
}

export interface NavigationMenu {
    id: string;
    title: string;
    icon: Component;
    items: NavigationLink[]
}

export interface NavigationSection {
    title: string;
    menus: NavigationMenu[]
}

function links(...slugs: string[]): NavigationLink[] {
    return slugs.map(slug => {
        const module = modules.find(item => item.slug === slug)
        if (!module) throw new Error('Unknown navigation module: ' + slug)
        return {slug, title: module.title}
    })
}

// Group navigation independently from the backend module registry and route definitions.
export const navigation: NavigationSection[] = [
    {
        title: 'Safari operations',
        menus: [
            {
                id: 'reservations', title: 'Reservations', icon: CalendarDays,
                items: links('inquiries', 'quotations', 'bookings', 'tourists')
            },
            {
                id: 'safari-planning', title: 'Safari planning', icon: Compass,
                items: links('trips', 'destinations', 'accommodations', 'vehicles')
            },
            {
                id: 'finance', title: 'Finance', icon: Wallet,
                items: links('invoices', 'receipts', 'refunds')
            },
            {
                id: 'website-content', title: 'Website content', icon: FileText,
                items: links('pages', 'blogs', 'faqs', 'testimonials', 'ratings', 'subscriptions')
            }
        ],
    },
    {
        title: 'Administration',
        menus: [
            {
                id: 'configuration', title: 'Configuration', icon: SlidersHorizontal,
                items: [...modules.filter(item => item.group === 'Configuration').map(({slug, title}) => ({
                    slug,
                    title
                })),
                    ...links('system_configurations')]
            },
            {
                id: 'locations', title: 'Locations', icon: MapPin,
                items: links('countries', 'regions', 'districts', 'locations')
            },
            {
                id: 'access', title: 'Access & permissions', icon: ShieldCheck,
                items: links('users', 'roles', 'permissions', 'menus')
            },
        ],
    },
]
