export default [
 {path:'/districts/list',name:'districts',component:()=>import('./DistrictsListPage.vue'),meta:{title:'Districts'}},
 {path:'/districts/:id/details',component:()=>import('./DistrictsDetailsPage.vue'),meta:{title:'Districts'}}
]
