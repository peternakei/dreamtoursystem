export default [
 {path:'/regions/list',name:'regions',component:()=>import('./RegionsListPage.vue'),meta:{title:'Regions'}},
 {path:'/regions/:id/details',component:()=>import('./RegionsDetailsPage.vue'),meta:{title:'Regions'}}
]
