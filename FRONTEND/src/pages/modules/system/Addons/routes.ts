export default [
 {path:'/addons/list',name:'addons',component:()=>import('./AddonsListPage.vue'),meta:{title:'Addons'}},
 {path:'/addons/:id/details',component:()=>import('./AddonsDetailsPage.vue'),meta:{title:'Addons'}}
]
