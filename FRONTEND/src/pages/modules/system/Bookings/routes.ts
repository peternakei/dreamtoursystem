export default [
 {path:'/bookings/list',name:'bookings',component:()=>import('./BookingsListPage.vue'),meta:{title:'Bookings'}},
 {path:'/bookings/:id/details',component:()=>import('./BookingsDetailsPage.vue'),meta:{title:'Bookings'}}
]
