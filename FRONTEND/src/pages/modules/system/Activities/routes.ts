export default [
 {path:'/activities/list',name:'activities',component:()=>import('./ActivitiesListPage.vue'),meta:{title:'Activities'}},
 {path:'/activities/:id/details',component:()=>import('./ActivitiesDetailsPage.vue'),meta:{title:'Activities'}}
]
