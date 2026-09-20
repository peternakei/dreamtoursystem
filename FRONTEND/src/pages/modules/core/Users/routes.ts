export default [
 {path:'/users/list',name:'users',component:()=>import('./UsersListPage.vue'),meta:{title:'Users'}},
 {path:'/users/:id/details',component:()=>import('./UsersDetailsPage.vue'),meta:{title:'Users'}}
]
