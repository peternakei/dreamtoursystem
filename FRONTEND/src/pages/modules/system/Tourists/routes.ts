export default [
 {path:'/tourists/list',name:'tourists',component:()=>import('./TouristsListPage.vue'),meta:{title:'Tourists'}},
 {path:'/tourists/:id/details',component:()=>import('./TouristsDetailsPage.vue'),meta:{title:'Tourists'}}
]
