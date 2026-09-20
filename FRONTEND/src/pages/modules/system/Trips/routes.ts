export default [
 {path:'/trips/list',name:'trips',component:()=>import('./TripsListPage.vue'),meta:{title:'Trips'}},
 {path:'/trips/:id/details',component:()=>import('./TripsDetailsPage.vue'),meta:{title:'Trips'}}
]
