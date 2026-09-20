export default [
 {path:'/roles/list',name:'roles',component:()=>import('./RolesListPage.vue'),meta:{title:'Roles'}},
 {path:'/roles/:id/details',component:()=>import('./RolesDetailsPage.vue'),meta:{title:'Roles'}}
]
