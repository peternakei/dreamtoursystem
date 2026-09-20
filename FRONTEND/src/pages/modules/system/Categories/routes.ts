export default [
 {path:'/categories/list',name:'categories',component:()=>import('./CategoriesListPage.vue'),meta:{title:'Categories'}},
 {path:'/categories/:id/details',component:()=>import('./CategoriesDetailsPage.vue'),meta:{title:'Categories'}}
]
