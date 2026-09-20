export default [
    {
        path: '/vehicles/list',
        name: 'vehicles',
        component: () => import('./VehiclesListPage.vue'),
        meta: {title: 'Vehicles'}
    },
    {
        path: '/vehicles/:id/details',
        component: () => import('./VehiclesDetailsPage.vue'),
        meta: {
            title: 'Vehicles'
        }
    }
]
