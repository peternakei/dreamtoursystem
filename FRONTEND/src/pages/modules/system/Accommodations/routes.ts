export default [
 {path:'/accommodations/list',name:'accommodations',component:()=>import('./AccommodationsListPage.vue'),meta:{title:'Accommodations'}},
 {path:'/accommodations/:id/details',component:()=>import('./AccommodationsDetailsPage.vue'),meta:{title:'Accommodations'}}
]
