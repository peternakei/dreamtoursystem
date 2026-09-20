export default [
 {path:'/permissions/list',name:'permissions',component:()=>import('./PermissionsListPage.vue'),meta:{title:'Permissions'}},
 {path:'/permissions/:id/details',component:()=>import('./PermissionsDetailsPage.vue'),meta:{title:'Permissions'}}
]
