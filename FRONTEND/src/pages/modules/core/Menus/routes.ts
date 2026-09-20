export default [
 {path:'/menus/list',name:'menus',component:()=>import('./MenusListPage.vue'),meta:{title:'Menus'}},
 {path:'/menus/:id/details',component:()=>import('./MenusDetailsPage.vue'),meta:{title:'Menus'}}
]
