export default [
 {path:'/ratings/list',name:'ratings',component:()=>import('./RatingsListPage.vue'),meta:{title:'Ratings'}},
 {path:'/ratings/:id/details',component:()=>import('./RatingsDetailsPage.vue'),meta:{title:'Ratings'}}
]
