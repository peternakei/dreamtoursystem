export default [
 {path:'/locations/list',name:'locations',component:()=>import('./LocationsListPage.vue'),meta:{title:'Locations'}},
 {path:'/locations/:id/details',component:()=>import('./LocationsDetailsPage.vue'),meta:{title:'Locations'}}
]
