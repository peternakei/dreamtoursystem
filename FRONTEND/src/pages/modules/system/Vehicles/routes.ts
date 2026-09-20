export default [
 {path:'/rental_offers/:id/details',component:()=>import('./RentalOffersPage.vue'),props:{details:true},meta:{title:'Rental offer'}},
 {path:'/rental_offers/list',name:'rental-offers',component:()=>import('./RentalOffersPage.vue'),meta:{title:'Car rental'}},
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
