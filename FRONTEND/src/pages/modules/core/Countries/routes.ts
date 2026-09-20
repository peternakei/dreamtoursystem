export default [
 {path:'/countries/list',name:'countries',component:()=>import('./CountriesListPage.vue'),meta:{title:'Countries'}},
 {path:'/countries/:id/details',component:()=>import('./CountriesDetailsPage.vue'),meta:{title:'Countries'}}
]
