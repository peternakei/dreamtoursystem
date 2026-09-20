export default [
 {path:'/pages/list',name:'pages',component:()=>import('./PagesListPage.vue'),meta:{title:'Pages'}},
 {path:'/pages/:id/details',component:()=>import('./PagesDetailsPage.vue'),meta:{title:'Pages'}}
]
