export default [
 {path:'/blogs/list',name:'blogs',component:()=>import('./BlogsListPage.vue'),meta:{title:'Blogs'}},
 {path:'/blogs/:id/details',component:()=>import('./BlogsDetailsPage.vue'),meta:{title:'Blogs'}}
]
