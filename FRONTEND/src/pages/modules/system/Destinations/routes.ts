export default [
 {path:'/destinations/list',name:'destinations',component:()=>import('./DestinationsListPage.vue'),meta:{title:'Destinations'}},
 {path:'/destinations/:id/details',component:()=>import('./DestinationsDetailsPage.vue'),meta:{title:'Destinations'}}
]
